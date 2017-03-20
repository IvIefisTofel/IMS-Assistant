<?php

namespace AuthDoctrine\Authentication\Service;

use AuthDoctrine\Authentication\Storage;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Service\Authentication\StorageFactory;

class StorageService extends StorageFactory
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

        if (is_string($storage = $options->getStorage())) {
            $options->setStorage($container->get($storage));
        }

        return new Storage($options);
    }

    /**
     * {@inheritDoc}
     *
     * @return \DoctrineModule\Authentication\Storage\ObjectRepository
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, Storage::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getOptionsClass()
    {
        return 'AuthDoctrine\Authentication\Options';
    }
}
