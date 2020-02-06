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

namespace LaminasUserLdapTest\ServiceFactory;

use Laminas\Mvc\Application;
use PHPUnit\Framework\TestCase;

class LdapAdapterFactoryTest extends TestCase
{

    protected $service_locator;
    protected $application;
    protected $applicationConfig;

    /**
     * Prepare the object to be tested.
     */
    protected function setUp():void
    {
        $config = include realpath(__DIR__ . '/../../config') . '/application.config.php';
        $this->applicationConfig = $config;
        $this->application = $this->getApplication();
    }

    public function testGetInstantiatedClassFromFactory()
    {
        $ldapAdapter = $this->application->getServiceManager()->get("LaminasUserLdap\LdapAdapter");
        $this->assertEquals(get_class($ldapAdapter), 'LaminasUserLdap\Adapter\Ldap');
    }

    /**
     * Get the application object
     * @return \Laminas\Mvc\ApplicationInterface
     */
    public function getApplication()
    {
        if ($this->application) {
            return $this->application;
        }
        $appConfig = $this->applicationConfig;
        $this->application = Application::init($appConfig);

        return $this->application;
    }
}
