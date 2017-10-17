<?php
namespace LibSSH2\Authentication;

interface Authentication
{
    /**
     * Authenticate against the remote resource.
     *  Errors/warnings are supressed
     *
     * @param  resource $resource remote connection resource
     * @return void
     */
    public function authenticate($resource);
}
