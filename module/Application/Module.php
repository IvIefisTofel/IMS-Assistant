<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use AuthDoctrine\Acl\Acl;
use Zend\View\Model\JsonModel;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
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

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function(MvcEvent $e) {
            if (method_exists($e->getResponse(), 'getStatusCode')) {
                if ($e->getResponse()->getStatusCode() == 404) {
                    $baseModel = $e->getViewModel();
                    $baseModel->setTemplate('error/layout');
                }
            }
        }, -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'onDispatchError'], -100);
        $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'onDispatchError'], -100);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => [
                'doctrine.connection.orm_default' => new \DoctrineORMModule\Service\DBALConnectionFactory('orm_default'),
                'doctrine.configuration.orm_default' => new \DoctrineORMModule\Service\ConfigurationFactory('orm_default'),
                'doctrine.entitymanager.orm_default' => new \DoctrineORMModule\Service\EntityManagerFactory('orm_default'),

                'doctrine.driver.orm_default' => new \DoctrineModule\Service\DriverFactory('orm_default'),
                'doctrine.eventmanager.orm_default' => new \DoctrineModule\Service\EventManagerFactory('orm_default'),
                'doctrine.entity_resolver.orm_default' => new \DoctrineORMModule\Service\EntityResolverFactory('orm_default'),
                'doctrine.sql_logger_collector.orm_default' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_default'),

                'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_default'));
                },
            ],
        );
    }

    function onDispatchError(MvcEvent $e)
    {
        $baseModel = $e->getViewModel();
        $baseModel->setTemplate('error/layout');
    }

    function onRoute(MvcEvent $e)
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $serviceManager = $application->getServiceManager();
        $auth = $serviceManager->get(\Zend\Authentication\AuthenticationService::class);
        $config = $serviceManager->get('Config');
        $acl = new Acl($config);

        $role = Acl::DEFAULT_ROLE;
        if ($auth->hasIdentity())
            $role = $auth->getIdentity()->getCurrentRole();

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        if (!$acl->hasResource($controller)) {
            throw new \Exception('Resource ' . $controller . ' is not defined in ./config/autoload/acl.global.php');
        }

        if (!$acl->isAllowed($role, $controller, $action)) {
            $eventManager = $application->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_DISPATCH, [$this, 'render404'], 1000);
        }
    }

    function render404(MvcEvent $e)
    {
        $request = $e->getApplication()->getServiceManager()->get('request');
        if ($request->isXmlHttpRequest() && $request->isPost()) {
            $view = new JsonModel([
                'error' => true,
                'message' => ACL::ACCESS_DENIED,
            ]);
            $e->setViewModel($view);
            $e->stopPropagation(true);
            return $view;
        } else {
            $routeMatch = $e->getRouteMatch();
            $routeMatch->setParam('controller', 'AuthDoctrine\Controller\Index');
            $routeMatch->setParam('action', 'accessdenied');
        }
    }
}