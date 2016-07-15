<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use AuthDoctrine\Acl\Acl;
use Zend\View\Model\ViewModel;
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
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach('route', array($this, 'onRoute'), -100);
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function(MvcEvent $event) {
            $baseModel = $event->getViewModel();
            $baseModel->setTemplate('error/layout');
        }, -200);
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => [
                'doctrine.connection.orm_default'           => new \DoctrineORMModule\Service\DBALConnectionFactory('orm_default'),
                'doctrine.configuration.orm_default'        => new \DoctrineORMModule\Service\ConfigurationFactory('orm_default'),
                'doctrine.entitymanager.orm_default'        => new \DoctrineORMModule\Service\EntityManagerFactory('orm_default'),

                'doctrine.driver.orm_default'               => new \DoctrineModule\Service\DriverFactory('orm_default'),
                'doctrine.eventmanager.orm_default'         => new \DoctrineModule\Service\EventManagerFactory('orm_default'),
                'doctrine.entity_resolver.orm_default'      => new \DoctrineORMModule\Service\EntityResolverFactory('orm_default'),
                'doctrine.sql_logger_collector.orm_default' => new \DoctrineORMModule\Service\SQLLoggerCollectorFactory('orm_default'),

                'DoctrineORMModule\Form\Annotation\AnnotationBuilder' => function(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    return new \DoctrineORMModule\Form\Annotation\AnnotationBuilder($sl->get('doctrine.entitymanager.orm_default'));
                },
            ],
        );
    }

    public function onRoute(\Zend\EventManager\EventInterface $e)
    {
        $application = $e->getApplication();
        $routeMatch = $e->getRouteMatch();
        $serviceManager = $application->getServiceManager();
        $auth = $serviceManager->get('Zend\Authentication\AuthenticationService');
        $config = $serviceManager->get('Config');
        $acl = new Acl($config);

        $role = Acl::DEFAULT_ROLE;
        if ($auth->hasIdentity())
            $role = $auth->getIdentity()->getUserRoleId();

        $controller = $routeMatch->getParam('controller');
        $action = $routeMatch->getParam('action');

        if (!$acl->hasResource($controller)) {
            throw new \Exception('Resource ' . $controller . ' is not defined in ./config/autoload/acl.global.php');
        }

        if (!$acl->isAllowed($role, $controller, $action)) {

            /**
             * Показать ошибку 404
             */
            $eventManager = $application->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_DISPATCH, function($e) {
                $routeMatch = $e->getRouteMatch();

                $routeMatch->setParam('controller', 'AuthDoctrine\Controller\Index');
                $routeMatch->setParam('action', 'accessdenied');

                if ($e->getRouteMatch()->getParam('__NAMESPACE__') == 'Admin\Controller' &&
                    $e->getApplication()->getServiceManager()->get('doctrine.authenticationservice.orm_default')->getIdentity()) {
                    $request = $e->getApplication()->getServiceManager()->get('request');
                    if ($request->isXmlHttpRequest() && $request->isPost()) {
                        $baseModel =  new JsonModel(array(
                            'error' => true,
                            'msgHeader' => 'Ошибка',
                            'message' => 'Не достаточно прав.',
                        ));
                        $e->setViewModel($baseModel);
                    } else {
                        $e->getViewModel()->setTemplate('layout/admin');
                        $e->setError('ACL_ACCESS_DENIED')->setParam('route', $routeMatch->getMatchedRouteName());
                        $e->getTarget()->getEventManager()->trigger('dispatch.error', $e);

                        $result = $e->getResult();

                        if ($result instanceof StdResponse) {
                            return;
                        }

                        /*$baseModel = $e->getViewModel();
                        $baseModel->setTemplate('layout/admin');

                        $model = new ViewModel();
                        $model->setTemplate('error/accessdenied');

                        $baseModel->addChild($model);
                        $baseModel->setTerminal(true);*/
                    }
                }
            }, 1000);

            /**
             * Редирект на главную
             */
//            $url = $e->getRouter()->assemble(array(), array('name' => 'home'));
//            $response = $e->getResponse();
//
//            $response->getHeaders()->addHeaderLine('Location', $url);
//
//            $response->setStatusCode(302);
//            $response->sendHeaders();
//            exit;
        }
    }
}