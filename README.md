LibSSH2
=======

[![Latest Stable Version](https://poser.pugx.org/degagne/libssh2/v/stable)](https://packagist.org/packages/degagne/libssh2) [![Latest Unstable Version](https://poser.pugx.org/degagne/libssh2/v/unstable)](https://packagist.org/packages/degagne/libssh2) [![License](https://poser.pugx.org/degagne/libssh2/license)](https://packagist.org/packages/degagne/libssh2) [![composer.lock](https://poser.pugx.org/degagne/libssh2/composerlock)](https://packagist.org/packages/degagne/libssh2) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/degagne/libssh2/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/degagne/libssh2/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/degagne/libssh2/badges/build.png?b=master)](https://scrutinizer-ci.com/g/degagne/libssh2/build-status/master) [![Code Intelligence Status](https://scrutinizer-ci.com/g/degagne/libssh2/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

LibSSH2 implements all supported SSH2 functions from the libssh2 extension.

Requirements
============

* PHP >= 5.4
* libssh2 Extension

Installation
============

Add libssh2 package to your composer.json file.
```bash
{
    "require": {
        "degagne/libssh2": "~1.0"
    }
}
```

or run
```composer require degagne/libssh2```

Usage
=====

```php
$configuration = (new Configuration())
    ->set_username('username')
    ->set_password('password')
    ->set_host('hostname');

$authentication = new Password($configuration);

$ssh = new SSH($configuration, $authentication);
$ssh->exec('ls -ltr');

$output = $ssh->get_output();     // stdout
$errors = $ssh->get_error();      // stderr
$retval = $ssh->get_exitstatus(); // return code
```

With Kerberos:
```php
$kerberos = new Kerberos($configuration, $authentication);
$krb_cache = $kerberos->kcreate('principle');

$ssh->exec("export KRBCCNAME={$krb_cache};ls -ltr");
```

Kerberos requires the use of the KRBCCNAME environmental variable to be set.
