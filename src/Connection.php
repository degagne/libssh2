<?php
namespace LibSSH2;

use LibSSH2\Authentication\Authentication;

/**
 * Connection class.
 *
 * Remote resource connection setter class.
 *
 * @package LibSSH2
 */
class Connection extends ConsoleOutput
{
    /**
     * Remote subsystem connection resource.
     *
     * @var resource
     */
    protected $connection;

    /**
     * Constructor.
     *
     * @param  instance $configuration  Configuration class instance
     * @param  instance $authentication Authentication class instance
     * @param  boolean  $tunnel         require SSH tunnel
     * @return void
     */
    public function __construct(Configuration $configuration, Authentication $authentication)
    {
        if (extension_loaded('ssh2') == false)
        {
            throw new \RuntimeException('The libssh2 extension is not loaded.');
        }

        $this->connect($configuration);
        $this->authenticate($authentication);

        if ($configuration->get_tunnel())
        {
            $this->tunnel($configuration);
        }
    }

    /**
     * Destructor.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Create remote connection resource.
     *
     * @param  resource $configuration \LibSSH2\Configuration object
     * @return void
     */
    final public function connect(Configuration $configuration)
    {
        $this->connection = @ssh2_connect($configuration->get_host(), $configuration->get_port(), $configuration->get_methods());
        if ($this->connection === FALSE || !is_resource($this->connection))
        {
            throw new \RuntimeException($this->get_error_message());
        }
    }

    /**
     * Create remote tunnel connection resource.
     *
     * @param  string $host hostname
     * @param  int    $port port (default=22)
     * @return void
     */
    final public function tunnel(Configuration $configuration)
    {
        $tunnel = @ssh2_tunnel($this->connection, $configuration->get_tunnel_host(), $configuration->get_tunnel_port());
        if ($tunnel === FALSE)
        {
            throw new \RuntimeException($this->get_error_message());
        }
    }

    /**
     * Authenticate remote connection resource.
     *
     * @param  resource $authentication \LibSSH2\Authentication\Authentication interface object
     * @return void
     */
    final public function authenticate(Authentication $authentication)
    {
        $authentication->authenticate($this->connection);
    }
    
    /**
     * {@inheritdoc}
     */
    final public function disconnect()
    {
        @ssh2_exec($this->connection, 'exit');
        unset($this->connection);
    }
}
