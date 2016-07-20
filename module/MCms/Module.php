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
                'siteYear' => View\Helper\GetSiteYear::class,
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
        $sm = $e->getApplication()->getServiceManager();
        $sm->setService('imagePlugin', $imagePlugin);
        $sm->setService('litHelper', $litHelper);


        // Table Prefix
        $tablePrefix = $sm->get('Config')['doctrine']['table_prefix'] ?? null;
        if ($tablePrefix !== null) {
            $evm = $sm->get('doctrine.eventmanager.orm_default');

            $tablePrefixExt = new \MCms\DoctrineExtension\TablePrefix($tablePrefix);
            $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefixExt);
        }
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