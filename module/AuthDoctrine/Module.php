<?php
namespace AuthDoctrine;

use Zend\ServiceManager\ServiceManager;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Validator\AbstractValidator;
use Zend\Mvc\MvcEvent;

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

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
				'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                    return $serviceManager->get('doctrine.authenticationservice.orm_default');
                },
				'mail.transport' => function (ServiceManager $serviceManager) {
					$config = $serviceManager->get('Config'); 
					$transport = new Smtp();                
					$transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
					return $transport;
				},
            )
        );
    }

    public function onBootstrap(MvcEvent $e)
    {
        $translator=$e->getApplication()->getServiceManager()->get('translator');
        $translator->addTranslationFile(
            'phpArray',
            'vendor/zendframework/zend-i18n-resources/languages/ru/Zend_Validate.php'
        );
        AbstractValidator::setDefaultTranslator($translator);
    }
}	