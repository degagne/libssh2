<?php
namespace LibSSH2\Sessions;

use LibSSH2\Authentication\Authentication;
use LibSSH2\Configuration;
use LibSSH2\Connection;
use LibSSH2\Terminal;

/**
 * SSH class.
 *
 * Execute remote commands via SSH.
 *
 * @package LibSSH2\Sessions
 */
class SSH extends Connection
{

    /**
     * Wait for command execution to finish.
     *
     * @var int
     */
    const WAIT = 'execute.wait';

    /**
     * Produce realtime output.
     *
     * @var int
     */
    const REALTIME = 'execute.realtime';

    /**
     * Write output to file (realtime).
     *
     * @var int
     */
    const FILE = 'execute.file';

    /**
     * Execution mode type.
     *
     * @var string
     */
    public $mode = NULL;

    /**
     * Filename path.
     *
     * @var string
     */
    public $filename = NULL;

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
    }

    /**
     * Set execution mode.
     *
     * @return object
     */
    final public function set_mode($mode = self::WAIT)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Set filename path.
     *
     * @return object
     */
    final public function set_filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Get execution mode.
     *
     * @return string execution mode type
     */
    final public function get_mode()
    {
        return ($this->mode === NULL) ? self::WAIT : $this->mode;
    }

    /**
     * Get filename path.
     *
     * @return string filename path
     */
    final public function get_filename()
    {
        return $this->filename;
    }

    /**
     * Execute remote command via SSH.
     *
     * @param  string   $command  command being executed
     * @param  instance $terminal Terminal instance
     * @return void
     */
    final public function exec($command, Terminal $terminal = NULL)
    {
        if (!$terminal instanceof Terminal)
        {
            $terminal = new Terminal();
        }

        switch ($this->get_mode())
        {
            case 'execute.wait':
                $stream = $this->get_stream($command, $terminal);
                $this->exec_wait($stream);
                break;

            case 'execute.realtime':
                $command .= ' 2>&1';
                $stream = $this->get_stream($command, $terminal);
                $this->exec_realtime($stream);
                break;

            case 'execute.file':
                $command .= ' 2>&1';
                $stream = $this->get_stream($command, $terminal);
                $this->exec_file($stream);
                break;

            default:
                throw new \RuntimeException('Unknown output mode type: ' . $this->get_mode());
        }
    }

    /**
     * Create channel stream.
     *
     * @param  string   $command  command being executed
     * @param  instance $terminal Terminal instance
     * @return stream   SSH connection resource stream
     */
    final private function get_stream($command, Terminal $terminal)
    {
        if (!is_resource($this->connection))
        {
            throw new \RuntimeException(__FUNCTION__ . ': not a valid SSH2 Session resource.');
        }

        $command .= '; echo "RETURN_CODE:[$?]"';
        $stream = @ssh2_exec(
            $this->connection,
            $command,
            $terminal->get_pty(),
            $terminal->get_env(),
            $terminal->get_width(),
            $terminal->get_height()
        );
        
        if ($stream === FALSE)
        {
            throw new \RuntimeException($this->get_error_message());
        }
        stream_set_blocking($stream, TRUE);
        return $stream;
    }

    /**
     * Executes a command on a remote server.
     *
     * @param  resource $stream SSH resource stream
     * @return void
     */
    final private function exec_wait($stream)
    {
        $out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
        $err = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

        $stdout = '';
        $stderr = '';
        do
        {
            sleep(1);
            if ($out === FALSE || $err === FALSE)
            {
                $stderr .= 'STDOUT and/or STDERR stream(s) closed unexpectedly.';
                return 1;
            }

            $stdout .= stream_get_contents($out);
            $stderr .= stream_get_contents($err);
        } while (!preg_match('/RETURN_CODE:\[([0-9]+)\]/', $stdout, $retval));

        fclose($out);
        fclose($err);

        $this->set_output(trim(preg_replace('/RETURN_CODE:\[([0-9]+)\]/', '', $stdout)));
        $this->set_error(trim($stderr));
        $this->set_exitstatus($retval[1]);
    }

    /**
     * Executes a command on a remote server (realtime output).
     *
     * @param  resource $stream SSH resource stream
     * @return void
     */
    final private function exec_realtime($stream)
    {
        while ($buffer = fgets($stream))
        {
            if (!preg_match('/RETURN_CODE:\[([0-9]+)\]/', $buffer, $retval))
            {
                print $buffer;
            }
            flush();
        }
        fclose($stream);
        $this->set_exitstatus($retval[1]);
    }

    /**
     * Executes a command on a remote server (writes output to local file).
     *
     * @param  resource $stream SSH resource stream
     * @return void
     */
    final private function exec_file($stream)
    {
        if ($this->get_filename() === NULL)
        {
            throw new \RuntimeException('A valid filename path must be provided.');
        }

        while ($line = fgets($stream))
        {
            flush();
            if (!preg_match('/RETURN_CODE:\[([0-9]+)\]/', $line, $retval))
            {
                file_put_contents($this->get_filename(), $line, FILE_APPEND | LOCK_EX);
            }
        }
        fclose($stream);

        $this->set_exitstatus($retval[1]);
    }
}
