<?php

/**
 * Copyright (c) 2020 Flávio Gomes da Silva Lisboa (https://github.com/fgsl/LaminasUserLdap)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 * 
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 *
 * 
 */

namespace LaminasUserLdap\ServiceFactory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class LdapAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('LaminasUserLdap\LdapConfig');
        $logger = $container->get('LaminasUserLdap\Logger');
        $zulconfig = $container->get('LaminasUserLdap\Config');
        
        return new \LaminasUserLdap\Adapter\Ldap($config, $logger, $zulconfig['logging']['log_enabled']);        
    }
    public function createService(ServiceLocatorInterface $serviceLocator)
    {}


}
