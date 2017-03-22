<?php

namespace Orders\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;
use Orders\Form\OrdersUpload as Form;
use MCms\Entity\Events as Event;
use Orders\Entity\Orders as Order;
use Nomenclature\Entity\Details as Detail;
use Files\Entity\Files as File;
use Files\Entity\NqFilesByCollections;
use Files\Entity\Collections as Collection;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }

        $error = false;
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $task   = $this->params()->fromRoute('task', null);
        $id     = $this->params()->fromRoute('id', null);

        $onlyNames = false;
        $task = str_replace(['only-names', 'onlynames'], '', strtolower($task), $countReplace);
        if ($countReplace == 1) {
            $onlyNames = true;
            if (substr($task, -1) == '-') {
                $task = substr($task, 0, -1);
            }
        }

        $clientName = null;
        $data = [];
        $flush = [];
        $postData = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
        $events = [];
        switch ($task) {
            case "update":
                /* @var $order Order */
                $order = $this->entityManager->getRepository('\Orders\Entity\Orders')->find($id);
                if (isset($postData['dropDetails'])) {
                    $this->plugin('DetailsPlugin')->drop($postData['dropDetails']);
                }
                goto order;
            case "add":
                $order = new Order();
                $add = true;
                order:
                $events['order'] = [
                    'order' => $order,
                    'events' => [],
                ];
                if (isset($postData['order'])) {
                    $edited = false;
                    if (($code = $postData['order']['code']) != $order->getCode()) {
                        $order->setCode($code);
                        $edited = true;
                    }
                    if (($clientId = $postData['order']['clientId']) != $order->getClientId()) {
                        $order->setClientId($clientId);
                        $edited = true;
                    }
                    if (($dateCreation = $postData['order']['dateCreation']) != $order->getDateCreation()) {
                        $order->setDateCreation($dateCreation);
                        $edited = true;
                    }
                    if (($dateStart = isset($postData['order']['dateStart']) ? $postData['order']['dateStart'] : null) != $order->getDateStart()) {
                        $event = new Event();
                        $event->setUserId($this->identity()->getId());
                        if ($order->getDateStart() == null && $dateStart != null) {
                            $event->setType(Event::E_ORDER_START);
                            if (isset($add)) {
                                $events['order']['events'][] = $event;
                            } else {
                                $event->setEntityId($order->getId());
                                $flush[] = $event;
                            }
                        } else {
                            $edited = true;
                        }
                        $order->setDateStart($dateStart);
                    }
                    if (($dateEnd = isset($postData['order']['dateEnd']) ? $postData['order']['dateEnd'] : null) != $order->getDateEnd()) {
                        $event = new Event();
                        $event->setUserId($this->identity()->getId());
                        if ($order->getDateEnd(false) == null && $dateEnd != null) {
                            $event->setType(Event::E_ORDER_END);
                        } elseif ($order->getDateEnd(false) != null && $dateEnd == null) {
                            $event->setType(EVENT::E_ORDER_RETURN);
                        } else {
                            $edited = true;
                        }
                        if ($event->getType() != null) {
                            if (isset($add)) {
                                $events['order']['events'][] = $event;
                            } else {
                                $event->setEntityId($order->getId());
                                $flush[] = $event;
                            }
                        }
                        $order->setDateEnd($dateEnd);
                    }
                    if (($dateDeadLine = isset($postData['order']['dateDeadline']) ? $postData['order']['dateDeadline'] : null) != $order->getDateDeadline()) {
                        $order->setDateDeadline($dateDeadLine);
                        $edited = true;
                    }

                    $orderStatus = 1;
                    if ($order->getDateEnd() != null) {
                        $orderStatus = 3;
                    } elseif ($order->getDateStart() != null) {
                        $orderStatus = 2;
                    }
                    if ($orderStatus != $order->getStatusCode()) {
                        $order->setStatus($orderStatus);
                        $edited = true;
                    }
                    if (isset($add) || $edited) {
                        $event = new Event();
                        $event->setUserId($this->identity()->getId());
                        if (isset($add)) {
                            $event->setType(Event::E_ORDER_CREATE);
                            $events['order']['events'][] = $event;
                        } else {
                            $event->setType(Event::E_ORDER_UPDATE);
                            $event->setEntityId($order->getId());
                            $flush[] = $event;
                        }
                    }

                    $this->entityManager->persist($order);
                    $this->entityManager->flush();

                    if (isset($events['order'])) {
                        foreach ($events['order']['events'] as $event) {
                            $event->setEntityId($order->getId());
                            $flush[] = $event;
                        }
                    }
                }
                unset($events['order']);

                $form = new Form('orderUpload');
                try {
                    if (isset($postData['details']['new'])) {
                        foreach ($postData['details']['new'] as $code => $detail) {
                            if (isset($postData['details']['new'][$code]['base64'])) {
                                foreach ($postData['details']['new'][$code]['base64'] as $key => $base64) {
                                    $fName = tempnam(File::UPLOAD_DIR, "img_");
                                    $fFile = fopen($fName, "wb");
                                    $data = explode(',', $base64);
                                    fwrite($fFile, base64_decode($data[1]));
                                    fclose($fFile);

                                    if ($key == 'first') {
                                        $postData['details']['new'][$code]['files']['first'] = [
                                            'ext' => 'jpg',
                                            'path' => pathinfo($fName)['basename']
                                        ];
                                    } else {
                                        $postData['details']['new'][$code]['files'][] = [
                                            'ext' => 'jpg',
                                            'path' => pathinfo($fName)['basename']
                                        ];
                                    }
                                }
                                unset($postData['details']['new'][$code]['base64']);
                            }
                        }
                    }
                    if (isset($postData['files'])) {
                        $files = [];
                        foreach ($postData['files'] as $code => $fileArr) {
                            foreach ($fileArr as $key => $file) {
                                $file['code'] = $code;
                                if ($key == 'first') {
                                    $file['first'] = true;
                                }
                                $files[] = $file;
                            }
                        }
                        unset($postData['files']);

                        $form->setData([Form::FILES => $files]);
                        $formData = null;
                        if ($form->isValid()) {
                            $formData = $form->getData();
                            foreach ($formData['files'] as $file) {
                                if (isset($file['first'])) {
                                    $postData['details']['new'][$file['code']]['files']['first'] = [
                                        'ext' => pathinfo($file['name'])['extension'],
                                        'path' => pathinfo($file['tmp_name'])['basename']
                                    ];
                                } else {
                                    $postData['details']['new'][$file['code']]['files'][] = [
                                        'ext' => pathinfo($file['name'])['extension'],
                                        'path' => pathinfo($file['tmp_name'])['basename']
                                    ];
                                }
                            }
                        }
                    }

                    //-------------------------------------------Import-------------------------------------------------
                    $newCollectionId = $this->plugin('FilesPlugin')->getLastCollectionId() + 1;
                    if (isset($postData['details']['import'])) {
                        $detailIDs = [];
                        foreach ($postData['details']['import'] as $id => $detail) {
                            $detailIDs[] = $id;
                        }

                        $fileCollectionIDs = [];
                        $details = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findById($detailIDs);
                        foreach ($details as $detail) {
                            /* @var $detail \Nomenclature\Entity\Details */
                            if ($detail->getOrderId() != null) {
                                $tmp = new Detail();
                                $tmp->setCode($postData['details']['import'][$detail->getId()]['code']);
                                $tmp->setName($postData['details']['import'][$detail->getId()]['name']);
                                $tmp->setOrderId($order->getId());
                                $tmp->setGroup($detail->getGroup());

                                if ($detail->getPattern() != null) {
                                    $tmp->setPattern($newCollectionId);
                                    $fileCollectionIDs[$newCollectionId] = $detail->getPattern();
                                    $newCollectionId++;
                                }

                                if ($detail->getModel() != null) {
                                    $tmp->setModel($newCollectionId);
                                    $fileCollectionIDs[$newCollectionId] = $detail->getModel();
                                    $newCollectionId++;
                                }

                                if ($detail->getProject() != null) {
                                    $tmp->setProject($newCollectionId);
                                    $fileCollectionIDs[$newCollectionId] = $detail->getProject();
                                    $newCollectionId++;
                                }

                                $flush[] = $tmp;

                                $event = new Event();
                                $event->setUserId($this->identity()->getId());
                                $event->setType(Event::E_DETAIL_CREATE);
                                $events[$detail->getId()]['detail'] = $tmp;
                                $events[$detail->getId()]['events'][] = $event;
                            } else {
                                $detail->setCode($postData['details']['import'][$detail->getId()]['code']);
                                $detail->setName($postData['details']['import'][$detail->getId()]['name']);
                                $detail->setOrderId($order->getId());
                                $flush[] = $detail;

                                $event = new Event();
                                $event->setUserId($this->identity()->getId());
                                $event->setType(Event::E_DETAIL_UPDATE);
                                $event->setEntityId($detail->getId());
                                $flush[] = $event;
                            }
                        }

                        if (count($fileCollectionIDs)) {
                            $prefix = $this->getServiceLocator()->get('Config')['doctrine']['table_prefix'];
                            $query = $this->entityManager->createNativeQuery(
                                NqFilesByCollections::getSql($fileCollectionIDs, $prefix),
                                NqFilesByCollections::getRsm()
                            );
                            $fileWithCollections = $query->getResult();
                            $fileByCollections = [];
                            foreach ($fileWithCollections as $file) {
                                /* @var $file \Files\Entity\NqFilesByCollections */
                                $fileByCollections[$file->getId()][$file->getFileId()] = $file;
                            }

                            $newFiles = [];

                            foreach ($flush as $detail) {
                                /* @var $detail \Nomenclature\Entity\Details */
                                /* @var $fileByCollection \Files\Entity\NqFilesByCollections */
                                if ($detail->getPattern() != null) {
                                    foreach ($fileByCollections[$fileCollectionIDs[$detail->getPattern()]] as $fileByCollection) {
                                        $file = new \Files\Entity\Files();
                                        $file->setName($fileByCollection->getName());
                                        $file->setParentId($fileByCollection->getId());

                                        $collection = new \Files\Entity\Collections();
                                        $collection->setId($detail->getPattern());

                                        $version = new \Files\Entity\FileVersion();
                                        $version->setPath($fileByCollection->getPath());
                                        $version->setDate($fileByCollection->getDate());

                                        $newFiles[] = [
                                            'collection' => $collection,
                                            'file' => $file,
                                            'version' => $version,
                                        ];
                                        $this->entityManager->persist($file);
                                    }
                                }
                                if ($detail->getModel() != null) {
                                    foreach ($fileByCollections[$fileCollectionIDs[$detail->getModel()]] as $fileByCollection) {
                                        $file = new \Files\Entity\Files();
                                        $file->setName($fileByCollection->getName());
                                        $file->setParentId($fileByCollection->getId());

                                        $collection = new \Files\Entity\Collections();
                                        $collection->setId($detail->getModel());

                                        $version = new \Files\Entity\FileVersion();
                                        $version->setPath($fileByCollection->getPath());
                                        $version->setDate($fileByCollection->getDate());

                                        $newFiles[] = [
                                            'collection' => $collection,
                                            'file' => $file,
                                            'version' => $version,
                                        ];
                                        $this->entityManager->persist($file);
                                    }
                                }
                                if ($detail->getProject() != null) {
                                    foreach ($fileByCollections[$fileCollectionIDs[$detail->getProject()]] as $fileByCollection) {
                                        $file = new \Files\Entity\Files();
                                        $file->setName($fileByCollection->getName());
                                        $file->setParentId($fileByCollection->getId());

                                        $collection = new \Files\Entity\Collections();
                                        $collection->setId($detail->getProject());

                                        $version = new \Files\Entity\FileVersion();
                                        $version->setPath($fileByCollection->getPath());
                                        $version->setDate($fileByCollection->getDate());

                                        $newFiles[] = [
                                            'collection' => $collection,
                                            'file' => $file,
                                            'version' => $version,
                                        ];
                                        $this->entityManager->persist($file);
                                    }
                                }
                            }
                            $this->entityManager->flush();

                            foreach ($newFiles as $newFile) {
                                $newFile['collection']->setFileId($newFile['file']->getId());
                                $newFile['version']->setFileId($newFile['file']->getId());
                                $flush[] = $newFile['collection'];
                                $flush[] = $newFile['version'];
                            }
                        }
                    }

                    //--------------------------------------------New---------------------------------------------------
                    if (isset($postData['details']['new'])) {
                        $fileByCollections = [];
                        foreach ($postData['details']['new'] as $code => $detail) {
                            $newDetail = new Detail();
                            $newDetail->setCode($code);
                            $newDetail->setName($detail['name']);
                            $newDetail->setOrderId($order->getId());

                            $event = new Event();
                            $event->setUserId($this->identity()->getId());
                            $event->setType(Event::E_DETAIL_CREATE);
                            $events[$code . $detail['name']]['detail'] = $newDetail;
                            $events[$code . $detail['name']]['events'][] = $event;

                            $newDetail->setPattern($newCollectionId);
                            $flush[] = $newDetail;

                            $collection = new Collection();
                            $collection->setId($newCollectionId);

                            $file = new File();
                            $file->setName($detail['name'] . '.' . $detail['files']['first']['ext']);
                            $this->entityManager->persist($file);

                            $version = new \Files\Entity\FileVersion();
                            $version->setPath($detail['files']['first']['path']);
                            unset($detail['files']['first']);

                            $fileByCollections[] = [
                                'collection' => $collection,
                                'file' => $file,
                                'version' => $version,
                            ];

                            foreach ($detail['files'] as $file) {
                                $collection = new Collection();
                                $collection->setId($newCollectionId);

                                $newFile = new File();
                                $newFile->setName($detail['name'] . '.' . $file['ext']);
                                $this->entityManager->persist($newFile);

                                $version = new \Files\Entity\FileVersion();
                                $version->setPath($file['path']);

                                $fileByCollections[] = [
                                    'collection' => $collection,
                                    'file' => $newFile,
                                    'version' => $version,
                                ];
                            }
                            $fileCollectionIDs[$newCollectionId] = [];
                            $newCollectionId++;
                        }
                        $this->entityManager->flush();

                        foreach ($fileByCollections as $colAndVers) {
                            $colAndVers['collection']->setFileId($colAndVers['file']->getId());
                            $colAndVers['version']->setFileId($colAndVers['file']->getId());
                            $this->entityManager->persist($colAndVers['collection']);
                            $this->entityManager->persist($colAndVers['version']);
                        }
                    }

                    foreach ($flush as $item) {
                        $this->entityManager->persist($item);
                    }
                    $this->entityManager->flush();
                    if (count($events)) {
                        foreach ($events as $eventDetail) {
                            /* @var $detail \Nomenclature\Entity\Details */
                            $detail = $eventDetail['detail'];
                            foreach ($eventDetail['events'] as $event) {
                                /* @var $event \MCms\Entity\Events */
                                $event->setEntityId($detail->getId());
                                $this->entityManager->persist($event);
                            }
                        }
                        $this->entityManager->flush();
                    }
                    if (isset($add)) {
                        $data = $order;
                    } else {
                        unset($data);
                        $status = true;
                    }
                } catch (\Exception $e) {
                    return new JsonModel(['error' => true, 'message' => $e->getMessage()]);
                }
                break;
            case "get":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $data = $this->entityManager->getRepository('\Orders\Entity\Orders')->find($id);
                }
                break;
            case "get-by-client": case "getbyclient": case "getByClient":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $clientName = $this->entityManager->getRepository('\Clients\Entity\Clients')->findOneById($id)->getName();
                    $data = $this->entityManager->getRepository('Orders\Entity\OrdersView')->findByClientId($id);
                }
                break;
            case "get-with-client": case "getwithclient": case "getWithClient":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $clientName = $this->entityManager->getRepository('\Clients\Entity\Clients')->findOneById($id)->getName();
                }
            default:
                $data = $this->entityManager->getRepository('Orders\Entity\OrdersView')->findBy([], ['dateCreation' => 'DESC']);
                break;
        }

        $result = [];
        if ($error) {
            $result = ["error" => $error];
        } else {
            if (isset($clientName) && $clientName != null) {
                $result['clientName'] = $clientName;
            }
            if (isset($data)) {
                $data = $this->plugin('OrdersPlugin')->toArray($data, ['onlyNames' => $onlyNames]);
                if (is_array($data) && count($data) == 1) {
                    $data = array_shift($data);
                }
                $result['data'] = $data;
            }
            if (isset($status)) {
                $result['status'] = $status;
            }
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}