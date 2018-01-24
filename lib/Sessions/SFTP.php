<?php
namespace LibSSH2\Sessions;

use LibSSH2\Authentication\Authentication;
use LibSSH2\Configuration;
use LibSSH2\Connection;

/**
 * SFTP class.
 *
 * SFTP interface.
 *
 * @package LibSSH2\Sessions
 */
class SFTP extends Connection
{

    /**
     * SFTP connection resource.
     *
     * @var resource
     */
    private $sftp;

    /**
     * Constructor.
     *
     * @param  instance $configuration  Configuration instance
     * @param  instance $authentication Authentication instance
     * @return void
     */
    public function __construct(Configuration $configuration, Authentication $authentication)
    {
        parent::__construct($configuration, $authentication);

        $this->sftp = @ssh2_sftp($this->connection);
        if (!$this->sftp)
        {
            throw new \RuntimeException($this->get_error_message());
        }
    }

    /**
     * Copies file.
     *
     * @param  string $srcfile  path to the source file
     * @param  string $destfile path to the destination file
     * @return void
     */
    final public function copy($srcfile, $destfile)
    {
        if ($this->is_file($srcfile) === FALSE)
        {
            $this->set_error('Local file '.$srcfile.' does not exist.');
            $this->set_exitstatus(1);
            return;
        }

        if (@copy($this->sftp_url($srcfile), $this->sftp_url($destfile)) === FALSE)
        {
            $this->set_error($this->get_error_message());
            $this->set_exitstatus(1);
            return;
        }
        $this->set_output('Successfully copied file at: '.$srcfile.' to '.$destfile);
        $this->set_exitstatus(0);
    }

    /**
     * Removes files.
     *
     * Caveats:
     *		-	accepts one or more files (recursive)
     *
     * @param  mixed $files files to remove
     * @return void
     */
    final public function delete($files)
    {
        $files = !is_array($files) ? [$files] : $files;
        foreach ($files as $file)
        {
            if ($this->is_file($file))
            {
                if (@unlink($this->sftp_url($file)) === false)
                {
                    $this->set_error($this->get_error_message());
                    $this->set_exitstatus(1);
                    return;
                }
                continue;
            }
        }
        $this->set_output('Successfully deleted remote file(s) at: '.implode(', ', $files));
        $this->set_exitstatus(0);
    }

    /**
     * Tells whether the filename is a directory.
     *
     * @param  string $path directory path
     * @return boolean
     */
    final public function is_dir($path)
    {
        return is_dir($this->sftp_url($path));
    }

    /**
     * Tells whether the filename is a file.
     *
     * @param  string $path directory path
     * @return boolean
     */
    final public function is_file($path)
    {
        return is_file($this->sftp_url($path));
    }

    /**
     * Directory listing.
     *
     * @param  string $path directory path
     * @return void
     */
    final public function ls($path)
    {
        $files = [];
        if ($handle = @opendir($this->sftp_url($path)))
        {
            while (($file = @readdir($handle)) !== false)
            {
                if ($file != '.' && $file != '..')
                {
                    $filename = rtrim($path, '/').'/'.$file;
                    if ($this->is_dir($filename))
                    {
                        $files['directories'][] = $filename;
                    }
					
                    if ($this->is_dir($filename) === false)
                    {
                        $files['files'][] = $filename;
                    }
                }
            }
            closedir($handle);
        }
        $this->set_output($files);
        $this->set_exitstatus(0);
    }

    /**
     * Returns pathnames matching a pattern (for files only & hidden files).
     *
     * @param  string $directory directory
     * @param  mixed  $pattern   pattern (does not support tilde expansion)
     * @return mixed  matched files or null
     */
    final public function glob($directory, $pattern = '')
    {
        if ($this->is_dir($directory) == false)
        {
            $this->set_error($this->get_error_message());
            $this->set_exitstatus(1);
            return;
        }
		
        $handle = opendir($this->sftp_url($directory));
        while (($file = readdir($handle)) !== false)
        {
            $files[] = preg_grep('/(^.*'.$pattern.'.*$)/', explode(PHP_EOL, $file));
        }

        if (empty($files))
        {
            $this->set_output([]);
            $this->set_exitstatus(0);
            return;
        }

        $files = array_reduce($files, 'array_merge', []);
        $files = array_diff($files, ['.', '..']);

        $_files = [];
        foreach ($files as $file)
        {
            $_files[] = rtrim($directory, '/').'/'.$file;
        }
        $this->set_output($_files);
        $this->set_exitstatus(0);
    }

    /**
     * Create new directory.
     *
     * @param  mizxed $directories path to the directory
     * @param  int    $mode        directory permission mode value (octal)
     * @param  bool   $recursive   create directories as needed
     * @param  int    $chgrp       change gid for directory
     * @return bool
     */
    final public function mkdir($directories, $mode = 0777, $recursive = false)
    {
        $directories = !is_array($directories) ? [$directories] : $directories;
        foreach ($directories as $directory)
        {
            if (@mkdir($this->sftp_url($directory), $mode, $recursive) === false)
            {
                $this->set_error($this->get_error_message());
                $this->set_exitstatus(1);
                return;
            }
        }
        $this->set_output('Successfully created remote directory(ies) at: '.implode(', ', $directories));
        $this->set_exitstatus(0);
    }

    /**
     * Moves file to different path (rename).
     *
     * @param  string $oldfile path to the old file
     * @param  string $newfile path to the new file
     * @return void
     */
    final public function rename($oldfile, $newfile)
    {
        if ($this->is_file($oldfile) === false)
        {
            $this->set_error('Local file '.$oldfile.' does not exist.');
            $this->set_exitstatus(1);
            return;
        }

        if (!@ssh2_sftp_rename($this->sftp, $oldfile, $newfile))
        {
            $this->set_error($this->get_error_message());
            $this->set_exitstatus(1);
            return;
        }
        $this->set_output('Successfully renamed remote file at: '.$oldfile.' to '.$newfile);
        $this->set_exitstatus(0);
    }

    /**
     * Removes directory.
     *
     * Caveats:
     *		-	directories that are not empty are emptied then deleted
     *		-	accepts one or more directories (recursive)
     *
     * @param  mixed   $directories directory or directories to remove
     * @return boolean
     */
    final public function rmdir($directories)
    {
        $directories = !is_array($directories) ? [$directories] : $directories;
        foreach ($directories as $directory)
        {
            if ($this->is_dir($directory))
            {
                start:
                $this->ls($directory)['files'];
                $stdout = $this->get_output();
                $files = array_key_exists('files', $stdout) ? $stdout['files'] : [];
                if (count($files) == 0)
                {
                    if (@rmdir($this->sftp_url($directory)) === false)
                    {
                        $this->set_error($this->get_error_message());
                        $this->set_exitstatus(1);
                        return;
                    }
                    continue;
                }

                if (count($files) != 0)
                {
                    $this->delete($files);
                    goto start;
                }
            }
        }
        $this->set_output('Successfully removed remote directory(ies) at: '.implode(', ', $directories));
        $this->set_exitstatus(0);
    }

    /**
     * Sends a file via SCP.
     *
     * @param  mixed   $local  		source file(s) (local)
     * @param  string  $remote_dir  destination directory (remote)
     * @param  integer $mode   		permissions on the new file
     * @return void
     */
    final public function put($local_files, $remote_dir, $mode = 0750)
    {
        $local_files = !is_array($local_files) ? [$local_files] : $local_files;
        foreach ($local_files as $local_file)
        {
            if (!@ssh2_scp_send($this->connection, $local_file, rtrim($remote_dir, '/').'/'.basename($local_file), $mode))
            {
                $this->set_error($this->get_error_message());
                $this->set_exitstatus(1);
                return;
            }
        }
        $this->set_output('Successfully sent local files to remote host at: '.implode(', ', $local_files));
        $this->set_exitstatus(0);
    }

    /**
     * Recieves a file via SCP.
     *
     * @param  mixed   $local  		source file(s) (local)
     * @param  string  $remote_dir  destination directory (remote)
     * @return void
     */
    final public function get($remote_files, $local_dir)
    {
        $remote_files = !is_array($remote_files) ? [$remote_files] : $remote_files;
        foreach ($remote_files as $remote_file)
        {
            if (!@ssh2_scp_recv($this->connection, $remote_file, rtrim($local_dir, '/').'/'.basename($remote_file)))
            {
                $this->set_error($this->get_error_message());
                $this->set_exitstatus(1);
                return;
            }
        }
        $this->set_output('Successfully received remote files: '.implode(', ', $remote_files));
        $this->set_exitstatus(0);
    }

    /**
     * Returns stat a file on a remote filesystem.
     *
     * @param  string $path directory path
     * @return void
     */
    final public function stat($path)
    {
        $statinfo = ssh2_sftp_stat($this->sftp, $path);

        if ($statinfo === false)	
        {
            $this->set_error($this->get_error_message());
            $this->set_exitstatus(0);
            return;
        }

        $statinfo = [
            'size' 		=> $statinfo['size'],
            'groupid'	=> $statinfo['gid'],
            'userid'	=> $statinfo['uid'],
            'atime'		=> date('c', $statinfo['atime']),
            'mtime'		=> date('c', $statinfo['mtime']),
            'mode'		=> $statinfo['mode'],
        ];
        $this->set_output($statinfo);
        $this->set_exitstatus(0);
    }

    /**
     * Create SFTP URL wrapper for unsupported commands.
     *
     * @param  string $path directory path
     * @return string SFTP connection wrapprer
     */
    final private function sftp_url($path = '')
    {
        return 'ssh2.sftp://'.$this->sftp.$path;
    }
	
}
