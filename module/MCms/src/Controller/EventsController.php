<?php

namespace MCms\Controller;

use Zend\View\Model\JsonModel;
use MCms\Entity\EventsView;
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
            $events = $this->entityManager->getRepository(EventsView::class)->findBy([],['date' => 'DESC'], $limit, $offset);
            foreach ($events as $key => $event) {
                /* @var $event \MCms\Entity\Events */
                $events[$key] = $event->toArray();
                $user = null;
                if ($event->getUserId() != null) {
                    $user = $this->entityManager->getRepository(\Users\Entity\Users::class)->find($event->getUserId());
                    /* @var $user \Users\Entity\Users */
                    $events[$key]['avatar'] = $user->getGrAvatar(80, false);
                }
                $events[$key]['date'] = $event->getDateFormat('Y-m-d');
                $events[$key]['css'] = Events::E_CLASSES[$event->getType()];
                $entityClass = Events::getEntityClass($event->getType());
                if ($entityClass === false) {
                    var_dump('errEventType');exit;
                }
                switch ($entityClass) {
                    case \Clients\Entity\Clients::class:
                        $client = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                        /* @var $client \Clients\Entity\Clients */
                        $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                        $events[$key]['message'] = str_replace('{client}', "<a href ui-sref=\"clients\">" . $client->getName() . "</a>", $msg);
                        break;
                    case \Orders\Entity\Orders::class:
                        $order = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                        /* @var $order \Orders\Entity\Orders */
                        if ($event->getType() != Events::E_DEADLINE_MISSED) {
                            $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                        } else {
                            $msg = Events::E_TEXTS[$event->getType()];
                        }
                        $events[$key]['message'] = str_replace('{order}', "<a href ui-sref=\"orders-custom({id: " . $order->getId() . "})\">" . $order->getCode() . "</a>", $msg);
                        break;
                    case \Nomenclature\Entity\Details::class:
                        if (in_array($event->getType(), [
                            Events::E_DEADLINE_MISSED,
                            Events::E_DETAIL_CREATE,
                            Events::E_DETAIL_UPDATE,
                            Events::E_DETAIL_END,
                        ])) {
                            $detail = $this->entityManager->getRepository($entityClass)->find($event->getEntityId());
                            /* @var $detail \Nomenclature\Entity\Details */
                            $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                            $events[$key]['message'] = str_replace('{detail}', "<a href ui-sref=\"nomenclature-custom({id: " . $detail->getId() . "})\">" . $detail->getCode() . ' (' . $detail->getName() . ')' . "</a>", $msg);
                            break;
                        }
                        if (in_array($event->getType(), [
                            Events::E_PATTERN_CREATE,
                            Events::E_PATTERN_UPDATE,
                            Events::E_MODEL_CREATE,
                            Events::E_MODEL_UPDATE,
                            Events::E_PROJECT_CREATE,
                            Events::E_PROJECT_UPDATE,
                        ])) {
                            $file = $this->entityManager->getRepository(\Files\Entity\Files::class)->find($event->getEntityId());
                            /* @var $file \Files\Entity\Files */
                            $msg = str_replace('{user}', "<b>" . $user->getFullName() . "</b>", Events::E_TEXTS[$event->getType()]);
                            $collection = $this->entityManager->getRepository(\Files\Entity\Collections::class)->findOneByFileId($file->getId());
                            /* @var $collection \Files\Entity\Collections */
                            switch ($event->getType()) {
                                case Events::E_PATTERN_CREATE: case Events::E_PATTERN_UPDATE:
                                    $detail = $this->entityManager->getRepository(\Nomenclature\Entity\Details::class)->findOneBy(['pattern'=>$collection->getId()]);
                                    $msg = str_replace('{detail}', "<a href ui-sref=\"nomenclature-custom({id: " . $detail->getId() . "})\">" . $detail->getCode() . ' (' . $detail->getName() . ')' . "</a>", $msg);
                                    break;
                                case Events::E_MODEL_CREATE: case Events::E_MODEL_UPDATE:
                                    $detail = $this->entityManager->getRepository(\Nomenclature\Entity\Details::class)->findOneBy(['model'=>$collection->getId()]);
                                    $msg = str_replace('{detail}', "<a href ui-sref=\"nomenclature-custom({id: " . $detail->getId() . "})\">" . $detail->getCode() . ' (' . $detail->getName() . ')' . "</a>", $msg);
                                    break;
                                case Events::E_PROJECT_CREATE: case Events::E_PROJECT_UPDATE:
                                    $detail = $this->entityManager->getRepository(\Nomenclature\Entity\Details::class)->findOneBy(['project'=>$collection->getId()]);
                                    $msg = str_replace('{detail}', "<a href ui-sref=\"nomenclature-custom({id: " . $detail->getId() . "})\">" . $detail->getCode() . ' (' . $detail->getName() . ')' . "</a>", $msg);
                                    break;
                            }
                            $events[$key]['message'] = str_replace('{file}', "<b>" . $file->getName().+ "</b>", $msg);
                            break;
                        }
                }
                $result = $events;
            }
        } catch (\Exception $e) {
            $errMsg = $e->getMessage();
        }

        if ($errMsg) {
            $result = ["error" => $errMsg];
        } elseif (!isset($result)) {
            $result['status'] = true;
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}