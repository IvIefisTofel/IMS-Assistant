<?php

namespace Orders\Controller\Plugin;

use MCms\Controller\Plugin\MCmsPlugin;

class OrdersPlugin extends MCmsPlugin
{
    public function toArray($orders = null, $options = [])
    {
        if ($orders !== null) {
            $onlyNames = isset($options['onlyNames']) ? $options['onlyNames'] : false;
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
                    if ($onlyNames) {
                        $result[$order->getId()] = [
                            'id' => $order->getId(),
                            'clientId' => $order->getClientId(),
                            'code' => $order->getCode(),
                        ];
                    } else {
                        $result[$order->getId()] = $order->toArray();
                        $result[$order->getId()]['statusCode'] = $order->getStatusCode();
                        $result[$order->getId()]['dateCreation'] = $order->getDateCreationFormat('Y-m-d');
                        $result[$order->getId()]['dateStart'] = $order->getDateStartFormat('Y-m-d');
                        $result[$order->getId()]['dateEnd'] = $order->getDateEndFormat('Y-m-d');
                        $result[$order->getId()]['dateDeadline'] = $order->getDateDeadlineFormat('Y-m-d');
                        if ($withDetails) {
                            $result[$order->getId()]['details'] = null;
                        }
                        $orderKeys[] = $order->getId();
                    }
                } else {
                    throw new \Exception('Array elements must be Order class.');
                }
            }

            if ($withDetails && !$onlyNames) {
                $details = $this->getController()->plugin('details')
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