<?php

namespace MCms\Controller\Plugin;

use MCms\Entity\Events;
use MCms\Entity\EventsView;

class EventsPlugin extends MCmsPlugin
{
    /**
     * @param array $options
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return EventsView[]|null
     */
    public function findBy($options = null, $orderBy = null, $limit = null, $offset = null)
    {
        $prefix = $this->getController()->getServiceLocator()->get('Config')['doctrine']['table_prefix'];
        if (isset($options['clients']) || isset($options['orders']) || isset($options['details'])) {
            $clientEvents = implode(",", Events::ARR_CLIENT_EVENTS);
            $orderEvents = implode(",", Events::ARR_ORDER_EVENTS);
            $detailEvents = implode(",", Events::ARR_DETAIL_EVENTS);

            $tree = isset($options['tree']) ? $options['tree'] : false;

            if (isset($options['details'])) {
                goto level2;
            }
            if (isset($options['orders'])) {
                goto level1;
            }

            /**
             * Clients
             */
            $sqlClientIds = "SELECT eventId as id FROM " . $prefix . "view_events WHERE eventType IN ($clientEvents) AND eventId IS NOT NULL";
            $clientIds = isset($options['clients']) ? $options['clients'] : null;
            if (is_array($clientIds)) {
                $clientIds = implode(",", $clientIds);
            }
            if ($clientIds != '' && $clientIds != null) {
                $sqlClientIds .= " AND eventEntity IN ($clientIds)";
            }

            if ($tree) {
                /**
                 * Orders
                 */
                level1:
                $orderIds = isset($options['orders']) ? $options['orders'] : null;
                $archive = false;
                if ($orderIds == null) {
                    $orderIds = "SELECT DISTINCT orderId FROM " . $prefix . "orders WHERE orderClientId IN ($clientIds)";
                } else {
                    if (is_array($orderIds)) {
                        foreach ($orderIds as $orderId) {
                            if ($orderId == -1) {
                                $archive = true;
                                break;
                            }
                        }
                        $orderIds = implode(",", $orderIds);
                    } else {
                        if ($orderIds == -1) {
                            $archive = true;
                        }
                    }
                }
                $sqlOrderIds = "SELECT eventId as id FROM " . $prefix . "view_events WHERE eventType IN ($orderEvents) " .
                    " AND eventEntity IN ($orderIds)";

                if ($tree) {
                    /**
                     * Details
                     */
                    level2:
                    $detailIds = isset($options['details']) ? $options['details'] : null;
                    if (isset($archive) && $archive == true) {
                        $archive = 'detailOrderId IS NULL';
                        if ($tree) {
                            $archive = 'OR ' . $archive;
                        } else {
                            $archive = 'AND ' . $archive;
                        }
                    } else {
                        $archive = '';
                    }
                    if ($detailIds == null) {
                        $detailIds = "SELECT DISTINCT detailId from " . $prefix . "details WHERE detailOrderId IN ($orderIds) $archive";
                    } else {
                        if (is_array($detailIds)) {
                            $detailIds = implode(",", $detailIds);
                        }
                    }
                    $sqlDetailIds = "SELECT eventId as id FROM " . $prefix . "view_events WHERE eventType IN ($detailEvents) " .
                        " AND eventEntity IN ($detailIds) AND eventId IS NOT NULL";
                }
            }

            $sql = isset($sqlClientIds) ? $sqlClientIds : '';
            if (isset($sqlOrderIds)) {
                if ($sql != '') {
                    $sql .= " UNION ";
                }
                $sql .= $sqlOrderIds;
            }
            if (isset($sqlDetailIds)) {
                if ($sql != '') {
                    $sql .= " UNION ";
                }
                $sql .= $sqlDetailIds;
            }

            $stmt = $this->entityManager->getConnection()->prepare($sql);
            $stmt->execute();
            $arrIds = $stmt->fetchAll();
            if (count($arrIds)) {
                foreach ($arrIds as $key => $item) {
                    $arrIds[$key] = $item['id'];
                }
            }
        }

        $opts = [
            'orderBy' => $orderBy,
            'limit' => $limit,
            'offset' => $offset,
        ];
        if (isset($arrIds)) {
            $opts['ids'] = $arrIds;
        }
        if (isset($options['users'])) {
            $opts['users'] = $options['users'];
        }
        if (isset($options['date'])) {
            $opts['date'] = $options['date'];
        }
        if ($orderBy != null && is_array($orderBy)) {
            foreach ($orderBy as $key => $item) {
                $orderBy[$key] = $this->entityManager->getClassMetadata('MCms\Entity\Events')->getColumnName($key) . " $item";
            }
            $orderBy = implode(", ", $orderBy);
            $opts['orderBy'] = $orderBy;
        }

        $sql = \MCms\Entity\EventsView::getSql($opts, $prefix);
        $query = $this->entityManager->createNativeQuery(\MCms\Entity\EventsView::getSql($opts, $prefix), \MCms\Entity\EventsView::getRsm());
        return $query->getResult();
    }
}