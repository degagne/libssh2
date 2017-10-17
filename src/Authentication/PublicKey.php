<?php
namespace LibSSH2\Authentication;

use LibSSH2\Configuration;

/**
 * PublicKey class.
 *
 * Public key based authentication.
 *
 * @package LibSSH2\Authentication
 */
class PublicKey extends Configuration implements Authentication
{
    /**
     * Username.
     *
     * @var string
     */
    protected $username;
    
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
     * Constructor.
     *
     * @param  object $configuration Configuration object
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        $this->username = $configuration->get_username();
        $this->pubkeyfile = $configuration->get_publickey();
        $this->privkeyfile = $configuration->get_privatekey();
        $this->passphrase = $configuration->get_passphrase();
    }

    /**
     * {@inheritDoc}
     */
    final public function authenticate($resource)
    {
        if (@ssh2_auth_pubkey_file(
            $resource,
            $this->username,
            $this->pubkeyfile,
            $this->privkeyfile,
            $this->passphrase
        ) === false)
        {
            throw new \RuntimeException('Public key based authentication failed.');
        }
    }
}
