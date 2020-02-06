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

namespace LaminasUserLdap\Provider\Identity;

use Laminas\Authentication\AuthenticationService;

class LaminasRbacIdentityProvider
{

    /**
     * Array of roles.
     *
     * @var array
     */
    protected $roles;

    /**
     * Array of configuration keys / values.
     *
     * @var array
     */
    protected $config;

    /**
     * @var AuthenticationService
     */
    protected $authService;

    /**
     * @var AuthenticationService
     */
    public function __construct(AuthenticationService $authService, array $config)
    {
        $roles = array();
        $this->authService = $authService;
        $this->config = $config;
        $roleKey = $this->config['identity_providers']['ldap_role_key'];
        if ($this->authService->hasIdentity()) {
            $rawObj = $this->authService->getIdentity()->getRawLdapObj();
            $data = @unserialize($rawObj);
            if ($data !== false) {
                $user = unserialize($rawObj);
                if (!is_null($user) || is_array($user)) {
                    $roles = array('user');
                    foreach ($user[$roleKey] as $role) {
                        //if (isset($definedRoles[$role]))
                        $roles[] = $role;
                    }
                }
            }
        }
        if (!is_array($roles)) {
            throw new \InvalidArgumentException('LaminasUserLdapRbacIdentityProvider only accepts strings or arrays');
        }
        $this->roles = $roles;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
