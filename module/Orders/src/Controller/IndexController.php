<?php

namespace Orders\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;
use Orders\Form\OrdersUpload as Form;
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

        $clientName = null;
        $data = [];
        $postData = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
        switch ($task) {
            case "update":
                /* @var $order Order */
                $order = $this->entityManager->getRepository('\Orders\Entity\Orders')->find($id);
                goto order;
            case "add":
                $order = new Order(); order:
                $order->setCode($postData['order']['code']);
                $order->setClientId($postData['order']['clientId']);
                $order->setDateCreation($postData['order']['dateCreation']);
                $order->setDateStart(isset($postData['order']['dateStart']) ? $postData['order']['dateStart'] : null);
                $order->setDateEnd(isset($postData['order']['dateEnd']) ? $postData['order']['dateEnd'] : null);
                $order->setDateDeadline(isset($postData['order']['dateDeadline']) ? $postData['order']['dateDeadline'] : null);
                $status = 1;
                if ($order->getDateEnd() != null) {
                    $status = 3;
                } elseif ($order->getDateStart() != null) {
                    $status = 2;
                }
                $order->setStatus($status);

                $this->entityManager->persist($order);
                $this->entityManager->flush();

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
                    $flush = [];
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
                        }

                        if (count($fileCollectionIDs)) {
                            $query = $this->entityManager->createNativeQuery(
                                NqFilesByCollections::getSql($fileCollectionIDs),
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
                    $data = $this->plugin('OrdersPlugin')->toArray($order);
                } catch (\Exception $e) {
                    return new JsonModel(['error' => true, 'message' => $e->getMessage()]);
                }
                break;
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('\Orders\Entity\Orders')->find($id));
                }
                break;
            case "get-by-client": case "getbyclient": case "getByClient":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $clientName = $this->entityManager->getRepository('\Clients\Entity\Clients')->findOneById($id)->getName();
                    $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('Orders\Entity\OrdersView')->findByClientId($id));
                }
                break;
            case "get-with-client": case "getwithclient": case "getWithClient":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $clientName = $this->entityManager->getRepository('\Clients\Entity\Clients')->findOneById($id)->getName();
                }
            default:
                $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('Orders\Entity\OrdersView')->findBy([], ['dateCreation' => 'DESC']));
                break;
        }

        if (count($data) == 1) {
            $data = $data[0];
        }

        $result = [];
        if ($error) {
            $result = ["error" => $error];
        } else {
            if (isset($clientName) && $clientName != null) {
                $result['clientName'] = $clientName;
            }
            if (isset($data) && count($data)) {
                $result['data'] = $data;
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