<?php

namespace MCms\Controller;

use Zend\View\Model\JsonModel;
use MCms\Entity\Events;

class EventsController extends MCmsController
{
    public function indexAction()
    {
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }

        $errMsg = false;
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $limit = 10;
        $offset = $this->params()->fromRoute('offset', 0);

        try {
            $opts = [];
            $user = $this->params()->fromPost('user', null);
            if ($user) { $opts['users'] = $user; }
            $client = $this->params()->fromPost('client', null);
            if ($client) { $opts['clients'] = $client; }
            $order = $this->params()->fromPost('order', null);
            if ($order) { $opts['orders'] = $order; }
            $detail = $this->params()->fromPost('detail', null);
            if ($detail) { $opts['details'] = $detail; }
            $date = $this->params()->fromPost('date', null);
            if ($date) { $opts['date'] = $date; }
            $tree = (bool)$this->params()->fromPost('tree', true);
            if ($tree) { $opts['tree'] = $tree; }

            $eventsPlugin = $this->plugin('EventsPlugin');
            /* @var $eventsPlugin  \MCms\Controller\Plugin\EventsPlugin */
            $events = $eventsPlugin->findBy($opts, ['date' => 'DESC'], $limit, $offset);
            foreach ($events as $key => $event) {
                /* @var $event \MCms\Entity\EventsView */
                $events[$key] = $event->toArray();
                $user = null;
                if ($event->getUserId() != null) {
                    $user = $this->entityManager->getRepository(\Users\Entity\Users::class)->find($event->getUserId());
                    /* @var $user \Users\Entity\Users */
                    $events[$key]['avatar'] = $user->getGrAvatar(80, false);
                }
                $events[$key]['date'] = $event->getDateFormat('Y-m-d H:i:s');
                $events[$key]['css'] = Events::E_CLASSES[$event->getType()];
                $entityClass = Events::getEntityClass($event->getType());
                switch ($entityClass) {
                    case \Clients\Entity\Clients::class:
                        $client = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                        /* @var $client \Clients\Entity\Clients */
                        $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                        $events[$key]['message'] = str_replace('{client}', "<a href='/#'>" . $client->getName() . "</a>", $msg);
                        break;
                    case \Orders\Entity\Orders::class:
                        $order = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                        /* @var $order \Orders\Entity\Orders */
                        if ($event->getType() != Events::E_DEADLINE_MISSED) {
                            $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                        } else {
                            $msg = Events::E_TEXTS[$event->getType()];
                        }
                        $events[$key]['message'] = str_replace('{order}', "<a href=\"/#/orders/nomenclature/" . $order->getId() . "\">" . $order->getCode() . "</a>", $msg);
                        break;
                    case \Nomenclature\Entity\Details::class:
                        $detail = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                        if ($detail != null) {
                            /* @var $detail \Nomenclature\Entity\Details */
                            $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                            if ($event->getType() == Events::E_DETAIL_ARCHIVED) {
                                $detailMsg = "<b>" . $detail->getCode() . ' (' . $detail->getName() . ")</b>";
                            } else {
                                $detailMsg = "<a href=\"/#/orders/nomenclature/detail/" . $detail->getId() . "\">" . $detail->getCode() . ' (' . $detail->getName() . ')' . "</a>";
                            }
                            $events[$key]['message'] = str_replace('{detail}', $detailMsg, $msg);
                        } else {
                            $msg = $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                            $events[$key]['message'] = str_replace('{detail}', "с идентификатором <b>" . $event->getEntityId(), $msg) . "</b><br>Деталь была удалена";
                        }
                        break;
                }
            }
            $result = ['data' => $events];
        } catch (\Exception $e) {
            $errMsg = $e->getMessage();
        }

        if ($errMsg) {
            $result = ["error" => $errMsg];
        } else {
            if (!isset($result)) {
                $result['status'] = true;
            }
            $result['count'] = $limit;
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}