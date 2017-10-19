## Installation

Add libssh2 package to your composer.json file.
```
{
    "require": {
        "degagne/libssh2": "~1.0"
    }
}
```

or run
```composer require degagne/libssh2```

## Basic Usage

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
