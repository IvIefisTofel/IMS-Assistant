<?php

namespace Clients\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ClientsPlugin extends AbstractPlugin
{
    public function toArray($clients = null, $options = [])
    {
        if ($clients !== null) {
            $onlyNames = isset($options['onlyNames']) ? $options['onlyNames'] : false;
            $withOrders = isset($options['withOrders']) ? $options['withOrders'] : false;
            $withFiles = isset($options['withFiles']) ? $options['withFiles'] : true;
            $allVersions = isset($options['allVersions']) ? $options['allVersions'] : false;
            $saveIds = isset($options['saveIds']) ? $options['saveIds'] : false;
            if ($onlyNames) {
                $withOrders = false;
                $withFiles = false;
                $allVersions = false;
                $saveIds = true;
            }

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
                    if ($onlyNames) {
                        $result[$client->getId()] = ['id' => $client->getId(), 'name' => $client->getName()];
                    } else {
                        if ($withOrders) {
                            $result[$client->getId()]['orders'] = null;
                        }
                        if ($withFiles && $client->getAdditions()) {
                            $collections[$client->getAdditions()] = true;
                        } elseif (!$withFiles) {
                            unset($result[$client->getId()]['additions']);
                        }
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

            if ($withFiles) {
                $collectionFiles = $this->controller->plugin('FilesPlugin')->getFiles(array_keys($collections), $allVersions);
                foreach ($result as $key => $client) {
                    if ($result[$key]['additions'] && isset($collectionFiles[$client['additions']])) {
                        $result[$key]['additions'] = array_values($collectionFiles[$client['additions']]);
                    } else {
                        $result[$key]['additions'] = null;
                    }
                }
            }

            if ($saveIds) {
                return $result;
            } else {
                return array_values($result);
            }
        }

        return null;
    }
}