<?php

/**
 * This file is part of the LaminasUserLdap Module (https://github.com/fgsl/LaminasUserLdap.git)
 *
 * Copyright (c) 2020 Flávio Gomes da Silva Lisboa (https://github.com/fgsl/LaminasUserLdap)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 */

namespace LaminasUserLdap;

use Laminas\Mvc\ModuleRouteListener;
use Laminas\Mvc\MvcEvent;

class Module {

    public function onBootstrap(MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig() {
        $moduleConfig = include __DIR__ . '/../config/module.config.php';
        $laminasUserConfig = @include __DIR__ . '/../config/laminasuserldap.global.php';
        return array_merge($moduleConfig,$laminasUserConfig);
    }

}
