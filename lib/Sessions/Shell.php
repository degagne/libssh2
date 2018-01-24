<?php
namespace LibSSH2\Sessions;

use LibSSH2\Authentication\Authentication;
use LibSSH2\Configuration;
use LibSSH2\Connection;

/**
 * Shell class.
 *
 * Shell interface.
 *
 * @package LibSSH2\Sessions
 */
class Shell extends Connection
{

    /**
     * SSH interactive shell resource.
     *
     * @var resource
     */
    private $stream;
    private $command;

    /**
     * Constructor.
     *
     * @param  object $configuration  Configuration class object
     * @param  object $authentication Authentication class object
     * @return void
     */
    public function __construct(Configuration $configuration, Authentication $authentication)
    {
        parent::__construct($configuration, $authentication);

        $this->shell();
    }
	
    /**
     * Destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        fclose($this->stream);
    }

    /**
     * Returns SSH interactive shell.
     *
     * @return resouce SSH interactive shell
     */
    final public function shell()
    {
        if (($this->stream = @ssh2_shell($this->connection)) === FALSE)
        {
            throw new \RuntimeException($this->get_error_message());
        }
        sleep(1);
        return $this;
    }

    /**
     * Retrieves shell command output.
     *
     * @return void
     */
    final public function output()
    {
        $stdout = [];
        while (!preg_match('/RETURN_CODE:\[([0-9]+)\]/', implode(PHP_EOL, $stdout), $retval))
        {
            $buffer = fgets($this->stream);
            if (!empty($buffer))
            {
                $stdout[] = trim($buffer);
                //print $buffer;
            }
        }
        $stdout = preg_replace('/RETURN_CODE:\[([0-9]+)\]/', '', $stdout);
        $this->set_output(implode(PHP_EOL, $stdout));
        $this->set_exitstatus($retval[1]);
    }

    /**
     * Execute remote command via SSH (shell).
     *
     * @param  string   $command  command being executed
     * @param  instance $terminal Terminal instance
     * @return object
     */
    final public function write($command, $returncode = FALSE)
    {
        $command = ($returncode == FALSE) ? $command : $command . '; echo "RETURN_CODE:[$?]";';
        $write_count = 0;
        $string_len = strlen($command . PHP_EOL);
        while ($write_count < $string_len)
        {
            $fwrite_count = fwrite($this->stream, substr($command . PHP_EOL, $write_count), 1024);
            if ($fwrite_count === FALSE)
            {
                throw new \RuntimeException('failed to write command to stream');
            }
            $write_count += $fwrite_count;
        }
        sleep(1);
        return $this;
    }
	
}
