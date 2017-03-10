<?php

namespace Orders\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class OrdersPlugin extends AbstractPlugin
{
    public function toArray($orders = null, $options = [])
    {
        if ($orders !== null) {
            $withDetails = isset($options['withDetails']) ? $options['withDetails'] : false;
            $allVersions = isset($options['allVersions']) ? $options['allVersions'] : false;
            $saveIds = isset($options['clearIds']) ? $options['clearIds'] : false;

            $result = [];
            if (!is_array($orders)) {
                $orders = [$orders];
            }

            $orderKeys = [];
            foreach ($orders as $key => $order) {
                if (in_array(get_class($order), ['Orders\Entity\Orders', 'Orders\Entity\OrdersView'])) {
                    /* @var $order \Orders\Entity\Orders */
                    $result[$order->getId()] = $order->toArray();
                    $result[$order->getId()]['dateCreation'] = $order->getDateCreationFormat('Y-m-d');
                    $result[$order->getId()]['dateStart'] = $order->getDateStartFormat('Y-m-d');
                    $result[$order->getId()]['dateEnd'] = $order->getDateEndFormat('Y-m-d');
                    $result[$order->getId()]['dateDeadline'] = $order->getDateDeadlineFormat('Y-m-d');
                    if ($withDetails) {
                        $result[$order->getId()]['details'] = null;
                    }
                    $orderKeys[] = $order->getId();
                } else {
                    throw new \Exception('Array elements must be Order class.');
                }
            }

            if ($withDetails) {
                $details = $this->getController()->plugin('DetailsPlugin')
                    ->toArray($this->entityManager->getRepository('Nomenclature\Entity\DetailsView')->findByOrderId($orderKeys, ['dateCreation' => 'DESC']), $allVersions);
                foreach ($details as $detail) {
                    $result[$detail['orderId']]['details'][] = $detail;
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