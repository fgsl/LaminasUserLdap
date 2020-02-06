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
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as LogWriter;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoggerAdapterFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('LaminasUserLdap\Config');
        $log_dir = $config['logging']['log_dir'];
        $log_filename = $config['logging']['log_filename'];
        if (!is_dir($log_dir)) {
            if (!mkdir($log_dir)) {
                throw new \Exception("Unable to create Log directory: $log_dir");
            }
        }
        $logger = new Logger;
        $writer = new LogWriter($log_dir . '/' . $log_filename);
        $logger->addWriter($writer);
        return $logger;
        
    }

}
