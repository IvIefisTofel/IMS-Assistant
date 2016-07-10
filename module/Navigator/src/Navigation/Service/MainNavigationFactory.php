<?php

namespace Navigator\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Interop\Container\ContainerInterface;

class MainNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return "mainNavigation";
    }

    protected function getPages(ContainerInterface $container)
    {
//        $serviceLocator = $container->get('controllerLoader')->getServiceLocator();
//        $navigation = [];
//
//        if (null === $this->pages) {
//            $om = $serviceLocator->get('Doctrine\ORM\EntityManager');
//            $menu = $om->getRepository('Menu\Entity\Menu')->findBy([], ['menuWeight' => 'ASC']);
//
//            if ($menu) {
//                foreach ($menu as $menuItem) {
//                    /* @var $menuItem \Menu\Entity\Menu */
//                    $navigation[] = [
//                        'label'  => $menuItem->getLabel(),
//                        'uri' => $menuItem->getUrl(),
//                    ];
//                }
//            }
//
//            $mvcEvent = $serviceLocator->get('Application')->getMvcEvent();
//
//            $routeMatch = $mvcEvent->getRouteMatch();
//            $router     = $mvcEvent->getRouter();
//            $pages      = $this->getPagesFromConfig($navigation);
//
//            $this->pages = $this->injectComponents(
//                $pages,
//                $routeMatch,
//                $router
//            );
//        }

        return $this->pages;
    }
}