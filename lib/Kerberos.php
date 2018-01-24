<?php
namespace LibSSH2;

use LibSSH2\Authentication\Authentication;
use LibSSH2\Builder;
use LibSSH2\Configuration;
use LibSSH2\Sessions\SSH;
use LibSSH2\Sessions\Shell;

/**
 * Kerberos class.
 *
 * Create and set the KRB5CCNAME environmental variable for 
 * Kerberos authentication.
 *
 * @package LibSSH2
 */
class Kerberos
{
    private $configuration;

    private $authentication;

    /**
     * Constructor.
     *
     * @param  instance $configuration  Configuration instance
     * @param  instance $authentication Authentication instance
     * @return void
     */
    public function __construct(Configuration $configuration, Authentication $authentication)
    {
        if (get_class($authentication) != 'LibSSH2\Authentication\Password')
        {
            throw new \RuntimeException("Kerberos authentication requires Password authentication to remote server.");
        }
        $this->configuration = $configuration;
        $this->authentication = $authentication;
    }

    /**
     * kcreate creates a Kerberos credential (ticket) cache and sets the
     * KRB5CCNAME environmental variable.
     *
     * @return string Kerberos credential cache
     */
    final public function kcreate($principle)
    {
        $username = $this->configuration->get_username();
        $command = (new Builder())
            ->setPrefix('kinit')
            ->setArguments([$principle]);

        $shell = new Shell($this->configuration, $this->authentication);
        $shell
            ->shell()
            ->write("export KRB5CCNAME=`mktemp /tmp/krb5cc_{$username}_XXXXXXXXXXXXX`;")
            ->write($command)
            ->write($this->configuration->get_password())
            ->write('echo KRB5CCNAME:$KRB5CCNAME', true)
            ->output();

        if (!preg_match("/KRB5CCNAME:(\/tmp\/krb5cc_.*)/", $shell->get_output(), $matches))
        {
            throw new \RuntimeException('Failed to create the Kerberos credential cache and set the KRB5CCNAME environmental variable.');
        }
        return $matches[1];
    }

    /**
     * The kdestroy utility destroys the user’s active Kerberos 
     * authorization tickets by overwriting and deleting the 
     * credentials cache that contains them. If the credentials 
     * cache is not specified, the default credentials cache is 
     * destroyed.
     *
     * @param  array  $options   klist command line options
     * @param  array  $arguments klist command line arguments
     * @return int    return code (exit status code)
     */
    final public function kdestroy(array $options = [], array $arguments = [], $strict = true)
    {
        $command = (new Builder())
            ->setPrefix('kdestroy')
            ->setOptions($options)
            ->setArguments($arguments);
        list($retval, $output) = $this->_exec($command, $strict);
        return $retval;
    }

    /**
     * klist lists the Kerberos principal and Kerberos tickets held 
     * in a credentials cache, or the keys held in a keytab file.
     *
     * @param  array  $options   klist command line options
     * @param  array  $arguments klist command line arguments
     * @return string klist results
     */
    final public function klist(array $options = [], array $arguments = [], $strict = true)
    {
        $command = (new Builder())
            ->setPrefix('klist')
            ->setOptions($options)
            ->setArguments($arguments);
        list($retval, $output) = $this->_exec($command, $strict);
        return $output;
    }

    /**
     * kinit obtains and caches an initial ticket-granting ticket for 
     * principal. If principal is absent, kinit chooses an appropriate 
     * principal name based on existing credential cache contents or 
     * the local username of the user invoking kinit. Some options 
     * modify the choice of principal name.
     *
     * @param  array  $options   kinit command line options
     * @param  array  $arguments kinit command line arguments
     * @return int    exit status code
     */
    final public function kinit(array $options = [], array $arguments = [], $strict = true)
    {
        $command = (new Builder())
            ->setPrefix('kinit')
            ->setOptions($options)
            ->setArguments($arguments);
        list($retval, $output) = $this->_exec($command, $strict);
        return $retval;
    }

    /**
     * Execute Kerberos command.
     *
     * @param  string  $command   command to be executed
     * @return array   return code and STDOUT
     */
    final private function _exec($command, $strict)
    {
        $ssh = new SSH($this->configuration, $this->authentication);
        $ssh->exec($command);

        $output = $ssh->get_output();
        $error = $ssh->get_error();
        $retval = $ssh->get_exitstatus();

        if ($strict && $retval != 0)
        {
            throw new \RuntimeException($error);
        }
        return [$retval, $output];
    }
}
