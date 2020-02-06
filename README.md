LaminasUserLdap
================

Laminas LaminasUser Extension to provide LDAP Authentication

It is a adaptation from ZfcUserLdap to Laminas

## Features
- Provides an adapter chain for LDAP authentication.
- Allows login by username & email address
- Provides automatic failover between configured LDAP Servers
- Provides an identity provider for BjyAuthorize
 
## TODO / In Progress
- Add ability to register a user in ldap
- Allow password resets in ldap

## Setup

The following steps are necessary to get this module working

  1. Run `php composer.phar require fgsl/laminas-user-ldap:1.0.0`
  2. Add `LaminasUserLdap` to the enabled modules list (Requires ZfcUser to be activated also)
  3. Add Laminas LDAP configuration to your autoload with key 'ldap' based on:
     https://docs.laminas.dev/laminas-ldap/intro/

     An example of the configuration is shown below for configs/autoload/global.php
     *Please make sure you do not include passwords in this file, I've included it
     for illustration purposes only*
    <pre class="brush:php">
    array(
    'ldap' => array(
        'server1' => array(
            'host'              => 's0.foo.net',
            'username'          => 'CN=user1,DC=foo,DC=net',
            'password'          => 'pass1',
            'bindRequiresDn'    => true,
            'accountDomainName' => 'foo.net',
            'baseDn'            => 'OU=Sales,DC=foo,DC=net',
        ),
        'server2' => array(
            'host'              => 's0.foo2.net',
            'username'          => 'CN=user1,DC=foo,DC=net',
            'password'          => 'pass1',
            'bindRequiresDn'    => true,
            'accountDomainName' => 'foo.net',
            'baseDn'            => 'OU=Sales,DC=foo,DC=net',
        ),
    )
    ),
      </pre>

## Application Configuration
Please make sure to enable both LaminasUser and LaminasUserLdap in your application.config.php as
shown below

<pre class="brush:php">
  array(
    'LaminasUser',
    'LaminasUserLdap',
    /* It's important to load LaminasUser before LaminasUserLdap as LaminasUserLdap is an addon to LaminasUser */
  );
</pre>

## LaminasUser Configuration

For the initial release please make sure to set the following settings in your
laminasuser configuration:
<pre class="brush:php">
    array(
        'enable_registration' => false,
        'enable_username' => true,
        'auth_adapters' => array( 100 => 'LaminasUserLdap\Authentication\Adapter\Ldap' ),
        'auth_identity_fields' => array( 'username','email' ),
    ),
</pre>

## Final notes

In the above configuration auth_identity_fields can be left as email only if you like,
however it's recommended to allow ldap users to be able to log in with their ldap uid.
enable_registration should however be turned off at this point as it will cause issues
when the user tries to sign up and it can't create the entity within LDAP.

There are some more error handling that needs to be done in the module, as well as
the ability to make modifications as per the zfcuser module abilities.  However this
has currently been disabled and you will not be able to *change* passwords.

Enjoy and if you find bugs or issues please add pull requests for the module.