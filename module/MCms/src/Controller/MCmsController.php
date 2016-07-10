<?php

namespace MCms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class MCmsController extends AbstractActionController
{
    protected $serviceLocator;
    protected $entityManager;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function indexAction()
    {
        $this->getResponse()->setStatusCode(404);
        return;
    }
}