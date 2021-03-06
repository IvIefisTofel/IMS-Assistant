<?php

namespace MCms\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class MCmsController extends AbstractActionController
{
    const INVALID_ID = "Error: id is invalid";

    /* @var $serviceLocator \Zend\ServiceManager\ServiceManager */
    protected $serviceLocator;
    /* @var $entityManager \Doctrine\ORM\EntityManager */
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

    /**
     * @return \Users\Entity\Users
    */
    public function identity()
    {
        return parent::identity();
    }
}