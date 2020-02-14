<?php
/**
 * Copyright (c) 2020 Flávio Gomes da Silva Lisboa (https://github.com/fgsl/LaminasUserLdap)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 *
 * @author Flávio Gomes da Silva Lisboa <flavio.lisboa@fgsl.eti.br>
 *
 */
namespace LaminasUserLdap\Authentication\Adapter;

use LaminasUserLdap\Mapper\User as UserMapperInterface;
use LaminasUser\Authentication\Adapter\AbstractAdapter;
use LaminasUser\Authentication\Adapter\AdapterChainEvent as AuthEvent;
use LaminasUser\Options\AuthenticationOptionsInterface;
use Laminas\Authentication\Result as AuthenticationResult;
use Laminas\Authentication\Exception\UnexpectedValueException as UnexpectedExc;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\EmailAddress;

class LdapAuth extends AbstractAdapter
{

    /**
     * @var UserMapperInterface
     */
    protected $mapper;

    /**
     * @var \Closure | callable
     */
    protected $credentialPreprocessor;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AuthenticationOptionsInterface
     */
    protected $options;

    /** @var \LaminasUserLdap\Entity\User */
    protected $entity;
    
    /** @var string **/
    protected $identity;
    
    /** @var string **/
    protected $credential;
    
    /** @var AuthEvent **/
    protected $e;
    
    /**
     * Method to keep compatibility with ZfcUserLdap
     * @param AuthEvent $e
     */
    public function authenticateEvent(AuthEvent $e)
    {
        $this->e = $e;
        $this->authenticate();
    }
    

    public function authenticate()
    {
        $userObject = null;
        $zulConfig = $this->serviceManager->get('LaminasUserLdap\Config');

        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            if ($this->e instanceof AuthEvent){
                $this->e->setIdentity($storage['identity'])
                        ->setCode(AuthenticationResult::SUCCESS)
                        ->setMessages(array('Authentication successful.'));
            }
            return;
        }

        // Get POST values
        $identity = $this->identity;
        $credential = $this->credential;
        
        if ($this->e instanceof AuthEvent){
            $identity = $this->e->getRequest()->getPost()->get('identity');
            $credential = $this->e->getRequest()->getPost()->get('credential');
        }

        // Start auth against LDAP
        $ldapAuthAdapter = $this->serviceManager->get('LaminasUserLdap\LdapAdapter');
        if ($ldapAuthAdapter->authenticate($identity, $credential) !== true) {
            // Password does not match
            if ($this->e instanceof AuthEvent){
                $this->e->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
                        ->setMessages(array('Supplied credential is invalid.'));
            }
            $this->setSatisfied(false);
            return false;
        }
        $validator = new EmailAddress();
        if ($validator->isValid($identity)) {
            $ldapObj = $ldapAuthAdapter->findByEmail($identity);
        } else {
            $ldapObj = $ldapAuthAdapter->findByUsername($identity);
        }
        if (!is_array($ldapObj)) {
            throw new UnexpectedExc('Ldap response is invalid returned: ' . var_export($ldapObj, true));
        }
        // LDAP auth Success!

        $this->getOptions()->getAuthIdentityFields();

        // Create the user object entity via the LDAP object
        $userObject = $this->getMapper()->newEntity($ldapObj);

        // If auto insertion is on, we will check against DB for existing user,
        // then will create or update user depending on results and settings
        if ($zulConfig['auto_insertion']['enabled']) {
            $validator = new EmailAddress();
            if ($validator->isValid($identity)) {
                $userDbObject = $this->getMapper()->findByEmail($identity);
            } else {
                $userDbObject = $this->getMapper()->findByUsername($identity);
            }

            if ($userDbObject === false) {
                $userObject = $this->getMapper()->updateDb($ldapObj, null);
            } elseif ($zulConfig['auto_insertion']['auto_update']) {
                $userObject = $this->getMapper()->updateDb($ldapObj, $userDbObject);
            } else {
                $userObject = $userDbObject;
            }
        }

        // Something happened that should never happen
        if (!$userObject) {
            if ($this->e instanceof AuthEvent){
                $this->e->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                        ->setMessages(array('A record with the supplied identity could not be found.'));
            }
            $this->setSatisfied(false);
            return false;
        }

        // We don't control state, however if someone manually alters
        // the DB, this will throw the code then
        if ($this->getOptions()->getEnableUserState()) {
            // Don't allow user to login if state is not in allowed list
            if (!in_array($userObject->getState(), $this->getOptions()->getAllowedLoginStates())) {
                if ($this->e instanceof AuthEvent){
                    $this->e->setCode(AuthenticationResult::FAILURE_UNCATEGORIZED)
                            ->setMessages(array('A record with the supplied identity is not active.'));
                }
                $this->setSatisfied(false);
                return false;
            }
        }

        // Set the roles for stuff like ZfcRbac
        $userObject->setRoles($this->getMapper()->getLdapRoles($ldapObj));
        // Success!
        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $userObject;
        $this->getStorage()->write($storage);
        if ($this->e instanceof AuthEvent){
            $this->e->setIdentity($userObject);
            $this->e->setCode(AuthenticationResult::SUCCESS)
                    ->setMessages(array('Authentication successful.'))
                    ->stopPropagation();
        }
    }

    /**
     * getMapper
     *
     * @return UserMapperInterface
     */
    public function getMapper()
    {
        if (null === $this->mapper) {
            $this->mapper = $this->getServiceManager()->get('LaminasUserLdap\Mapper');
        }
        return $this->mapper;
    }

    /**
     * setMapper
     *
     * @param UserMapperInterface $mapper
     * @return LdapAuth
     */
    public function setMapper(UserMapperInterface $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param AuthenticationOptionsInterface $options
     */
    public function setOptions(AuthenticationOptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * @return AuthenticationOptionsInterface
     */
    public function getOptions()
    {
        if (!$this->options instanceof AuthenticationOptionsInterface) {
            $this->setOptions($this->getServiceManager()->get('LaminasUser_module_options'));
        }
        return $this->options;
    }

    /**
     * @return AuthenticationOptionsInterface
     */
    public function getEntity()
    {
        $entityClass = $this->getOptions()->getUserEntityClass();
        $this->entity = new $entityClass;
        return $this->entity;
    }
    
    /**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }
    
    /**
     * @param string $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
        return $this;
    }
}
