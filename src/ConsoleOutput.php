<?php
namespace LibSSH2;

/**
 * ConsoleOutput class.
 *
 * Setter/getter class for console output.
 *
 * @package LibSSH2
 */
class ConsoleOutput
{

    /**
     * STDOUT stream.
     *
     * @var string
     */
    protected $stdout;

    /**
     * STDERR stream.
     *
     * @var string
     */
    protected $stderr;

    /**
     * Exit status code.
     *
     * @var int
     */
    protected $exitstatus;
    
    /**
     * Last known error message.
     *
     * @var string
     */
    protected $error_message = null;
    
    /**
     * Last known error line number.
     *
     * @var int
     */
    protected $error_line_no = null;
    
    /**
     * Last known error filename.
     *
     * @var string
     */
    protected $error_file = null;

    /**
     * Set STDOUT output stream.
     *
	 * @param  mixed $stdout standard output
     * @return object
     */
    final public function set_output($stdout)
    {
		$this->stdout = $stdout;
    }

    /**
     * Set STDERR output stream.
     *
	 * @param  string $stderr standard error
     * @return object
     */
    final public function set_error($stderr)
    {
        $this->stderr = $stderr;
    }

    /**
     * Set exit status code.
     *
	 * @param  int $exitstatus exit status code
     * @return object
     */
    final public function set_exitstatus($exitstatus)
    {
        $this->exitstatus = $exitstatus;
    }

    /**
     * Get STDOUT output stream.
     *
     * @return mixed STDOUT output stream
     */
    final public function get_output()
    {
		return $this->stdout;
    }

    /**
     * Get STDERR output stream.
     *
     * @return string STDERR output stream
     */
    final public function get_error()
    {
		return $this->stderr;
    }

    /**
     * Get exit status code.
     *
     * @return int
     */
    final public function get_exitstatus()
    {
		return $this->exitstatus;
    }
    
    /**
     * Get last known 'fatal' error.
     *
     * @return string
     */
    final public function get_error_message()
    {
        $this->error_message = error_get_last()['message'];
        return $this->error_message . ' in ' . $this->get_error_file() . ' on line ' . $this->get_error_line();
    }
    
    /**
     * Get last known 'fatal' error.
     *
     * @return string
     */
    final public function get_error_line()
    {
        $this->error_line_no = error_get_last()['line'];
        return $this->error_line_no;
    }
    
    /**
     * Get last known 'fatal' error.
     *
     * @return string
     */
    final public function get_error_file()
    {
        $this->error_file = error_get_last()['file'];
        return $this->error_file;
    }
}
