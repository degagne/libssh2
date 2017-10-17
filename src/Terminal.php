<?php
namespace LibSSH2;

/**
 * Terminal class.
 *
 * Setter/getter class for terminal environment.
 *
 * @package LibSSH2
 */
class Terminal
{

    /**
     * Interactive connection (pseudo-tty)
     *
     * @var string
     */
    private $pty = null;

    /**
     * Environmental variables (associative array).
     *
     * @var array
     */
    private $env = [];

    /**
     * Width of the virtual terminal.
     *
     * @var int
     */
    private $width = 80;

    /**
     * Height of the virtual terminal.
     *
     * @var int
     */
    private $height = 25;

    /**
     * Sets interactive connection (pseudo-tty).
     *
     * @param  string $pty pseudo-tty
     * @return object
     */
    final public function set_pty($pty)
    {
        $this->pty = $pty;
        return $this;
    }

    /**
     * Sets environmental variables.
     *
     * @param  array  $env environmental variables
     * @return object
     */
    final public function set_env($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * Sets width of virtual terminal.
     *
     * @param  int    $width width of virtual terminal
     * @return object
     */
    final public function set_width($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Sets height of virtual terminal.
     *
     * @param  int    $height height of virtual terminal
     * @return object
     */
    final public function set_height($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Gets interactive connection (pseudo-tty).
     *
     * @return string
     */
    final public function get_pty()
    {
        return $this->pty;
    }

    /**
     * Gets environmental variables.
     *
     * @return array
     */
    final public function get_env()
    {
        return $this->env;
    }

    /**
     * Gets width of virtual terminal.
     *
     * @return int
     */
    final public function get_width()
    {
        return $this->width;
    }

    /**
     * Gets height of virtual terminal.
     *
     * @return int
     */
    final public function get_height()
    {
        return $this->height;
    }
}
