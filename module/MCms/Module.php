<?php
namespace MCms;

use MCms\Logger\Formatter;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

class Module implements ConsoleUsageProviderInterface
{
    private $assets;

    public function getConfig()
    {
        require_once ('Libs/AssetsGenerator.php');
        $this->assets = new \AssetsGenerator();
        return array_merge(include __DIR__ . '/config/module.config.php', $this->assets->getAssets());
    }
	
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/',
                ],
            ],
        ];
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'getCssCollection' => View\Helper\GetCssCollection::class,
                'getJsCollection' => View\Helper\GetJsCollection::class,
                'siteYear' => View\Helper\GetSiteYear::class,
                'numEnding' => View\Helper\NumEnding::class,
                'phoneFormat' => View\Helper\PhoneFormat::class,
                'ucfirst' => View\Helper\UcFirst::class,
                'lcfirst' => View\Helper\LcFirst::class,
            ],
        ];
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Logger' => function($sm){
                    $logger = new \Zend\Log\Logger;
                    $writer = new \Zend\Log\Writer\Stream('./data/log/'. date('Y-m-d') . '_error.log');
                    $logger->addWriter($writer);
                    return $logger;
                },
            ]
        ];
    }

    public function onBootstrap(MvcEvent $e)
    {
        require_once ('Libs/CSSMin.php');
        require_once ('Libs/JSMin.php');
        require_once ('Libs/ImagePlugin.php');
        require_once ('Libs/php-mo.php');
        require_once ('Libs/Console.php');

        $imagePlugin = new \ImagePlugin();
        $litHelper = new \LitHelperPlugin();
        $sm = $e->getApplication()->getServiceManager();
        $sm->setService('imagePlugin', $imagePlugin);
        $sm->setService('litHelper', $litHelper);
        if (isset($this->assets) && $this->assets != null) {
            $sm->setService('assets', $this->assets);
        }

        // Table Prefix
        $tablePrefix = $sm->get('Config')['doctrine']['table_prefix'] ?? null;
        if ($tablePrefix !== null) {
            $evm = $sm->get('doctrine.eventmanager.orm_default');

            $tablePrefixExt = new \MCms\DoctrineExtension\TablePrefix($tablePrefix);
            $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefixExt);
        }

        if (PHP_SAPI !== 'cli' && !($e->getRequest() instanceof \Zend\Console\Request)) {
            /** @var \Zend\EventManager\EventManagerInterface $eventManager */
            $eventManager = $e->getTarget()->getEventManager();
            $eventManager->attach(MvcEvent::EVENT_RENDER_ERROR, [$this, 'exceptionHandler']);
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, [$this, 'exceptionHandler']);
            $eventManager->attach(MvcEvent::EVENT_RENDER, [$this, 'exceptionHandler']);
        }
    }

    function exceptionHandler(MvcEvent $e)
    {
        if ($e->isError()) {
            $reason = $e->getError();
        } elseif (is_array($reason = $e->getResult()->getVariables()) && count($reason)) {
            $reason = $reason['reason'];
        }

        if (isset($reason) && $reason != null) {
            switch ($reason) {
                case Application::ERROR_CONTROLLER_CANNOT_DISPATCH:
                case Application::ERROR_CONTROLLER_NOT_FOUND:
                case Application::ERROR_CONTROLLER_INVALID:
                    $msg = Formatter::controllerException($e);
                    break;
                case Application::ERROR_ROUTER_NO_MATCH:
                    $msg = Formatter::routeNoMatchException($e);
                    break;
                default:
                    $msg = Formatter::defaultException($e);
                    break;
            }

            if ($msg != null) {
                $em = $e->getApplication()->getServiceManager()->get('Doctrine\ORM\EntityManager');
                /* @var $em \Doctrine\ORM\EntityManager */

                $writeErr = true;
                $date = new \DateTime();
                $oldErr = $em->getRepository('MCms\Entity\Errors')->findByHash(md5($msg));
                foreach ($oldErr as $err) {
                    /* @var $err \MCms\Entity\Errors */
                    if ($err->getDate()->format('d.m.Y') == $date->format('d.m.Y')) {
                        $writeErr = false;
                    }
                }

                if ($writeErr) {
                    $error = new \MCms\Entity\Errors();
                    $error->setMessage($msg);
                    $em->persist($error);
                    $em->flush();
                }
            }
        }
    }

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'compile-mo' => 'Generate binary language *.mo files from *.po files',
        ];
    }
}