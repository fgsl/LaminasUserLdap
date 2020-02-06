<?php

/**
 * This file is part of the LaminasUserLdap Module (https://github.com/fgsl/laminas-user-ldap.git)
 *
 * Copyright (c) 2020 Flávio Gomes da Silva Lisboa (https://github.com/fgsl/laminas-user-ldap)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 */
return array(
    'service_manager' => array(
        'invokables' => array(
            'LaminasUserLdap\Adapter\Ldap' => 'LaminasUserLdap\Adapter\Ldap',
            'LaminasUserLdap\Authentication\Adapter\LdapAuth' => 'LaminasUserLdap\Authentication\Adapter\LdapAuth',
        ),
        'aliases' => array(
        ),
        'factories' => array(
            'LaminasUserLdap\Config' => 'LaminasUserLdap\ServiceFactory\LaminasUserLdapConfigFactory',
            'LaminasUserLdap\LdapAdapter' => 'LaminasUserLdap\ServiceFactory\LdapAdapterFactory',
            'LaminasUserLdap\LdapConfig' => 'LaminasUserLdap\ServiceFactory\LdapConfigFactory',
            'LaminasUserLdap\Logger' => 'LaminasUserLdap\ServiceFactory\LoggerAdapterFactory',
            'LaminasUserLdap\Mapper' => 'LaminasUserLdap\ServiceFactory\UserMapperFactory',
            'LaminasUserLdap\Provider\Identity\LdapIdentityProvider' => 'LaminasUserLdap\Service\LdapIdentityProviderFactory',
            'LaminasUserLdap\LaminasRbacIdentityProvider' => 'LaminasUserLdap\ServiceFactory\LaminasRbacIdentityProviderFactory',
        )
    ),
);
