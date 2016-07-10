<?php
namespace MCms;

use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

class Module implements ConsoleUsageProviderInterface
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

    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'numEnding' => View\Helper\NumEnding::class,
                'phoneFormat' => View\Helper\PhoneFormat::class,
                'ucfirst' => View\Helper\UcFirst::class,
                'lcfirst' => View\Helper\LcFirst::class,
            ),
        );
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
        $e->getApplication()->getServiceManager()->setService('imagePlugin', $imagePlugin);
        $e->getApplication()->getServiceManager()->setService('litHelper', $litHelper);
    }

    /**
     * This method is defined in ConsoleUsageProviderInterface
     */
    public function getConsoleUsage(Console $console)
    {
        return array(
            'compile-mo' => 'Generate binary language *.mo files from *.po files',
        );
    }
}