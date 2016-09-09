<?php

namespace Clients\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ClientsPlugin extends AbstractPlugin
{
    public function toArray($clients = null, $withOrders = false, $allVersions = false)
    {
        if ($clients !== null) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $result = [];

            if (!is_array($clients)) {
                $clients = [$clients];
            }

            $collections = [];
            foreach ($clients as $client) {
                if (get_class($client) == 'Clients\Entity\Clients') {
                    /* @var $client \Clients\Entity\Clients */
                    $result[$client->getId()] = $client->toArray();
                    if ($withOrders) {
                        $result[$client->getId()]['orders'] = null;
                    }
                    if ($client->getAdditions()) {
                        $collections[$client->getAdditions()] = true;
                    }
                } else {
                    throw new \Exception('Array elements must be Clients\Entity\Clients class.');
                }
            }

            if ($withOrders) {
                $orders = $em->getRepository('Orders\Entity\Orders')->findByClientId(array_keys($result), ['dateCreation' => 'DESC']);
                foreach ($orders as $order) {
                    /* @var $val \Orders\Entity\Orders */
                    $result[$order->getClientId()]['orders'][$order->getId()] = $order->toArray();
                }
            }

            $collectionFiles = $this->controller->plugin('FilesPlugin')->getFiles(array_keys($collections), $allVersions);
            foreach ($result as $key => $client) {
                if ($result[$key]['additions']) {
                    $result[$key]['additions'] = array_values($collectionFiles[$client['additions']]);
                }
            }

            return array_values($result);
        }

        return null;
    }
}