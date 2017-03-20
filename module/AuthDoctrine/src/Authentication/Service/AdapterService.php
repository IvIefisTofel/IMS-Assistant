<?php

namespace AuthDoctrine\Authentication\Service;

use AuthDoctrine\Authentication\Adapter;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Service\Authentication\AdapterFactory;

class AdapterService extends AdapterFactory
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options \AuthDoctrine\Authentication\Options */
        $options = $this->getOptions($container, 'authentication');

        if (is_string($objectManager = $options->getObjectManager())) {
            $options->setObjectManager($container->get($objectManager));
        }

        return new Adapter($options);
    }

    /**
     * {@inheritDoc}
     *
     * @return \DoctrineModule\Authentication\Adapter\ObjectRepository
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Adapter::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return 'AuthDoctrine\Authentication\Options';
    }
}
