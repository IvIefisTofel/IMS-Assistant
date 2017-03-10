<?php

namespace MCms\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\DispatchableInterface as Dispatchable;

abstract class MCmsPlugin extends AbstractPlugin
{
    /* @var $entityManager \Doctrine\ORM\EntityManager */
    protected $entityManager;

    public function setController(Dispatchable $controller)
    {
        $this->controller = $controller;
        $this->entityManager = $controller->getServiceLocator()->get('Doctrine\ORM\EntityManager');
    }
}