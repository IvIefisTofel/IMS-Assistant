<?php

namespace AuthDoctrine\Acl;

use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Users\Entity\Users;

class Acl extends ZendAcl {
    /**
     * Default Role
     */
    const DEFAULT_ROLE = GUEST_ROLE;
    const ACCESS_DENIED = 'Не достаточно прав для выполнения данной операции.';
    /**
     * md5 (Ural_SM_Assistant)
    */
    const DEV_CODE = 'b2fd2929e062086b475774a8ecd215e4';

    /**
     * Constructor
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        if (!isset($config['acl']['roles']) || !isset($config['acl']['resources'])) {
            throw new \Exception('Invalid ACL Config found');
        }

        $roles = $config['acl']['roles'];
        if (!isset($roles[self::DEFAULT_ROLE])) {
            $roles[self::DEFAULT_ROLE] = '';
        }

        $this->_addRoles($roles)
             ->_addResources($config['acl']['resources']);
    }

    /**
     * Adds Roles to ACL
     *
     * @param array $roles
     * @return \AuthDoctrine\Acl
     */
    protected function _addRoles($roles)
    {
        foreach ($roles as $name => $parent) {
            if (!$this->hasRole($name)) {
                if (empty($parent)) {
                    $parent = array();
                } else {
                    $parent = explode(',', $parent);
                }

                $this->addRole(new Role($name), $parent);
            }
        }

        return $this;
    }

    /**
     * Adds Resources to ACL
     *
     * @param $resources
     * @return \AuthDoctrine\Acl
     * @throws \Exception
     */
    protected function _addResources($resources)
    {
        foreach ($resources as $permission => $controllers) {
            foreach ($controllers as $controller => $actions) {
                if ($controller == 'all') {
                    $controller = null;
                } else {
                    if (!$this->hasResource($controller)) {
                        $this->addResource(new Resource($controller));
                    }
                }

                foreach ($actions as $action => $role) {
                    if ($action == 'all') {
                        $action = null;
                    }

                    if ($permission == 'allow') {
                        $this->allow($role, $controller, $action);
                    } elseif ($permission == 'deny') {
                        $this->deny($role, $controller, $action);
                    } else {
                        throw new \Exception('No valid permission defined: ' . $permission);
                    }
                }
            }
        }

        return $this;
    }
}
