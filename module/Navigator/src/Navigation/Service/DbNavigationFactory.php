<?php

namespace Navigator\Navigation\Service;

use Zend\Navigation\Service\AbstractNavigationFactory;
use Interop\Container\ContainerInterface;

class DbNavigationFactory extends AbstractNavigationFactory
{
    public function getName()
    {
        return "dbNavigation";
    }

    protected function getPages(ContainerInterface $container)
    {
//        $serviceLocator = $container->get('controllerLoader')->getServiceLocator();
//        $navigation = array();
//
//        if (null === $this->pages) {
//            $om = $serviceLocator->get('Doctrine\ORM\EntityManager');
//            $repo = $om->getRepository('Tecdoc\Entity\TofManufacturers');
//            $manuf = $repo->findBy(array(), array('mfaBrand' => 'ASC'));
//
//            if ($manuf) {
//                foreach ($manuf as $key => $val) {
//                    /* @var $val \Tecdoc\Entity\TofManufacturers */
//                    $navigation[] = array(
//                        'label' => ucfirst(mb_strtolower($val->getMfaBrand(), 'utf-8')),
//                        'route' => 'models',
//                        'params' => [
//                            'mfaId' => $val->getMfaId(),
//                        ],
//                        'imageId' => $val->getMfaId(),
//                    );
//                }
//            }
//
//            $mvcEvent = $serviceLocator->get('Application')
//                ->getMvcEvent();
//
//            $routeMatch = $mvcEvent->getRouteMatch();
//            $router = $mvcEvent->getRouter();
//            $pages = $this->getPagesFromConfig($navigation);
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