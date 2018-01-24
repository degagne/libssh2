<?php
namespace LibSSH2;

/**
 * Configuration class.
 *
 * Remote resource configuration settings.
 *
 * @package LibSSH2
 */
class Configuration
{

    /**
     * Username.
     *
     * @var string
     */
    protected $username = null;
	
    /**
     * User password.
     *
     * @var string
     */
    protected $password = null;

    /**
     * RSA public key.
     *
     * @var string
     */
    protected $publickey = null;
	
    /**
     * RSA private key.
     *
     * @var string
     */
    protected $privatekey = null;
	
    /**
     * Passphrase.
     *
     * @var string
     */
    protected $passphrase = null;

    /**
     * Hostname.
     *
     * @var string
     */
    protected $host = null;

    /**
     * Port.
     *
     * @var integer
     */
    protected $port = 22;

    /**
     * Remote connection methods.
     *
     * @var array
     */
    protected $methods = [];
    
    /**
     * SSH tunnel requested.
     *
     * @var boolean
     */
    protected $tunnel = FALSE;

    /**
     * SSH tunnel hostname.
     *
     * @var string
     */
    protected $tunnel_host = NULL;
    
    /**
     * SSH tunnel port.
     *
     * @var int
     */
    protected $tunnel_port = 22;

    /**
     * Sets username.
     *
     * @param  string $username username
     * @return object \LibSSH2\Configuration object
     */
    final public function set_username($username)
    {
        $this->username = $username;
        return $this;
    }
	
    /**
     * Sets user password.
     *
     * @param  string $password password
     * @return object \LibSSH2\Configuration object
     */
    final public function set_password($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Sets RSA public key.
     *
     * @param  string $publickey RSA public key
     * @return object \LibSSH2\Configuration object
     */
    final public function set_publickey($public_key)
    {
        $this->publickey = $public_key;
        return $this;
    }

    /**
     * Sets RSA private key.
     *
     * @param  string $privatekey RSA private key
     * @return object \LibSSH2\Configuration object
     */
    final public function set_privatekey($private_key)
    {
        $this->privatekey = $private_key;
        return $this;
    }
	
    /**
     * Sets passphrase.
     *
     * @param  string $passphrase passphrase
     * @return object \LibSSH2\Configuration object
     */
    final public function set_passphrase($passphrase)
    {
        $this->passphrase = $passphrase;
        return $this;
    }

    /**
     * Sets hostname.
     *
     * @param  string $host hostname
     * @return object \LibSSH2\Configuration object
     */
    final public function set_host($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Sets port.
     *
     * @param  int    $port port
     * @return object \LibSSH2\Configuration object
     */
    final public function set_port($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Sets methods.
     *
     * @param  array  $methods remote connection methods
     * @return object \LibSSH2\Configuration object
     */
    final public function set_methods(array $methods = NULL)
    {
        if ($methods !== NULL)
        {
            $this->methods = $methods;
        }
        return $this;
    }

    /**
     * Sets tunnel.
     *
     * @return object \LibSSH2\Configuration object
     */
    final public function set_tunnel()
    {
        $this->tunnel = TRUE;
        return $this;
    }

    /**
     * Sets tunnel hostname.
     *
     * @param  string $hostname hostname
     * @return object \LibSSH2\Configuration object
     */
    final public function set_tunnel_host($host)
    {
        $this->set_tunnel();
        $this->tunnel_host = $host;
        return $this;
    }

    /**
     * Sets tunnel port.
     *
     * @param  int    $port port
     * @return object \LibSSH2\Configuration object
     */
    final public function set_tunnel_port($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * Returns username.
     *
     * @return string username
     */
    final public function get_username()
    {
        if ($this->username === NULL)
        {
            throw new \RuntimeException('A username is required to authenticate to the remote server.');
        }
        return $this->username;
    }

    /**
     * Returns user's password.
     *
     * @return string password
     */
    final public function get_password()
    {
        if ($this->password === NULL)
        {
            throw new \RuntimeException('Password is not currently set.');
        }
        return $this->password;
    }

    /**
     * Returns RSA public key.
     *
     * @return string RSA public key
     */
    final public function get_publickey()
    {
        if ($this->publickey === NULL)
        {
            throw new \RuntimeException('No RSA public key found.');
        }
        return $this->publickey;
    }
	
    /**
     * Returns RSA private key.
     *
     * @return string privatekey
     */
    final public function get_privatekey()
    {
        if ($this->privatekey === NULL)
        {
            throw new \RuntimeException('No RSA private key found.');
        }
        return $this->privatekey;
    }
	
    /**
     * Returns passphrase.
     *
     * @return string passphrase
     */
    final public function get_passphrase()
    {
        return ($this->passphrase === NULL) ? '' : $this->passphrase;
    }

    /**
     * Returns hostname.
     *
     * @return string hostname
     */
    final public function get_host()
    {
        if ($this->host === NULL)
        {
            throw new \RuntimeException('Unable to create remote connection; no hostname was set.');
        }
        return $this->host;
    }

    /**
     * Returns port.
     *
     * @return int port
     */
    final public function get_port()
    {
        return $this->port;
    }

    /**
     * Returns remote connection methods.
     *
     * @return array methods
     */
    final public function get_methods()
    {
        if ($this->methods !== NULL || !empty($this->methods))
        {
            return $this->methods;
        }
    }

    /**
     * Returns tunnel.
     *
     * @return boolean
     */
    final public function get_tunnel()
    {
        return $this->tunnel;
    }

    /**
     * Returns tunnel hostname.
     *
     * @return string tunnel hostname
     */
    final public function get_tunnel_host()
    {
        if ($this->tunnel_host === NULL)
        {
            throw new \RuntimeException('A valid hostname must be set prior to attempting a tunnel connection.');
        }
        return $this->tunnel_host;
    }
    
    /**
     * Returns tunnel port.
     *
     * @return int port
     */
    final public function get_tunnel_port()
    {
        return $this->tunnel_port;
    }
}