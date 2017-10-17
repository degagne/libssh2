<?php
namespace LibSSH2\Authentication;

use LibSSH2\Configuration;

/**
 * Password class.
 *
 * Username and password based authentication.
 *
 * @package LibSSH2\Authentication
 */
class Password extends Configuration implements Authentication
{
    /**
     * Username.
     *
     * @var string
     */
    protected $username;

    /**
     * Password.
     *
     * @var string
     */
    protected $password;

    /**
     * Constructor.
     *
     * @param  object $configuration Configuration object
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        $this->username = $configuration->get_username();
        $this->password = $configuration->get_password();
    }

    /**
     * {@inheritDoc}
     */
    final public function authenticate($resource)
    {
        if (@ssh2_auth_password($resource, $this->username, $this->password) === false)
        {
            throw new \RuntimeException('Password based authentication failed.');
        }
    }
}
