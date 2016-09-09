<?php

namespace Orders\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class OrdersPlugin extends AbstractPlugin
{
    public function toArray($orders = null, $withDetails = false, $allVersions = false)
    {
        if ($orders !== null) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $result = [];

            if (!is_array($orders)) {
                $orders = [$orders];
            }

            $orderKeys = [];
            foreach ($orders as $key => $order) {
                if (get_class($order) == 'Orders\Entity\Orders') {
                    /* @var $order \Orders\Entity\Orders */
                    $result[$order->getId()] = $order->toArray();
                    if ($withDetails) {
                        $result[$order->getId()]['details'] = null;
                    }
                    $orderKeys[] = $order->getId();
                } else {
                    throw new \Exception('Array elements must be Orders\Entity\Orders class.');
                }
            }

            if ($withDetails) {
                $details = $this->getController()->plugin('DetailsPlugin')
                    ->toArray($em->getRepository('Nomenclature\Entity\Details')->findByOrderId($orderKeys, ['dateCreation' => 'DESC']), $allVersions);
                foreach ($details as $detail) {
                    $result[$detail['orderId']]['details'][] = $detail;
                }
            }

            return array_values($result);
        }

        return null;
    }
}