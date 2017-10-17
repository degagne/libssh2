<?php
namespace LibSSH2\Authentication;

use LibSSH2\Configuration;
/**
 * None class.
 *
 * Username based authentication.
 *
 * @package LibSSH2\Authentication
 */
class None extends Configuration implements Authentication
{
    /**
     * Username.
     *
     * @var string
     */
    protected $username;

    /**
     * Constructor.
     *
     * @param  object $configuration Configuration object
     * @return void
     */
    public function __construct(Configuration $configuration)
    {
        $this->username = $configuration->get_username();
    }

    /**
     * {@inheritDoc}
     */
    final public function authenticate($resource)
    {
        $auth = @ssh2_auth_none($resource, $this->username);
        if (is_array($auth))
        {
            throw new \RuntimeException('Username based authentication failed, supported methods include: ' . implode(', ', $auth));
        }
    }
}
