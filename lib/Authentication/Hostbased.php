<?php
namespace LibSSH2\Authentication;

use LibSSH2\Configuration;
/**
 * Hostbased class.
 *
 * Public hostkey based authentication.
 *
 * @package LibSSH2\Authentication
 */
class Hostbased extends Configuration implements Authentication
{
    /**
     * Username.
     *
     * @var string
     */
    protected $username;
    
    /**
     * Hostname.
     *
     * @var string
     */
    protected $hostname;
    
    /**
     * Public key file.
     *
     * @var string
     */
    protected $pubkeyfile;
    
    /**
     * Private key file.
     *
     * @var string
     */
    protected $privkeyfile;

    /**
     * Passphrase.
     *
     * @var string
     */
    protected $passphrase;
    
    /**
     * Local username.
     *
     * @var string
     */
    protected $local_username;

    /**
     * Constructor.
     *
     * @param  object $configuration Configuration object
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        $this->username = $configuration->get_username();
        $this->hostname = $configuration->get_host();
        $this->pubkeyfile = $configuration->get_publickey();
        $this->privkeyfile = $configuration->get_privatekey;
        $this->passphrase = $configuration->get_passphrase;
        $this->local_username = $configuration->get_username();
    }

    /**
     * {@inheritDoc}
     */
    final public function authenticate($resource)
    {
        if (@ssh2_auth_hostbased_file(
            $resource,
            $this->username,
            $this->hostname,
            $this->pubkeyfile,
            $this->privkeyfile,
            $this->passphrase,
            $this->local_username
        ) === FALSE)
        {
            throw new \RuntimeException('Hostbased file authentication failed.');
        }
    }
}
