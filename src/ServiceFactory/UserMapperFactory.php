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
/**
 * LaminasUserLdap Module
 *
 * @package    LaminasUserLdap
 */

namespace LaminasUserLdap\ServiceFactory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Hydrator;

/**
 * @package    LaminasUserLdap
 */
class UserMapperFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get('LaminasUser_module_options');
        
        $mapper = new \LaminasUserLdap\Mapper\User();
        $entityClass = $options->getUserEntityClass();
        $mapper->setEntityPrototype(new $entityClass);
        $mapper->setDbAdapter($container->get('LaminasUser_Laminas_db_adapter'));
        $mapper->setHydrator(new Hydrator\ClassMethods);
        
        return $mapper;        
    }
}
