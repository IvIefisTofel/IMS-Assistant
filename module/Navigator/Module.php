<?php

namespace Navigator;

use Zend\View\HelperPluginManager;

class Module
{
    public function getConfig()
    {
        return array();
    }
	
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/',
                ),
            ),
        );
    }
	
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'navigation' => function(HelperPluginManager $pm) {
					$sm = $pm->getServiceLocator();
					$config = $sm->get('Config');

					$acl = new \AuthDoctrine\Acl\Acl($config);

					// Get the AuthenticationService
					$auth = $sm->get('Zend\Authentication\AuthenticationService');

					$role = \AuthDoctrine\Acl\Acl::DEFAULT_ROLE; // The default role is guest $acl
					if ($auth->hasIdentity())
                        $role = $auth->getIdentity()->getUserRoleId();

                    $navigation = $pm->get('Zend\View\Helper\Navigation');

                    $navigation->setAcl($acl)->setRole((string)$role);

                    return $navigation;
                }
            )
        );
    }
	
}