<?php
namespace LibSSH2\Authentication;

use LibSSH2\Configuration;

/**
 * Agent class.
 *
 * SSH agent based authentication.
 *
 * @package LibSSH2\Authentication
 */
class Agent extends Configuration implements Authentication
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
        if (@ssh2_auth_agent($resource, $this->username) === FALSE)
        {
            throw new \RuntimeException('Agent based authentication failed.');
        }
    }
}
