<?php

namespace Nomenclature\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;
use Nomenclature\Form\DetailsUpload as Form;
use Files\Entity\Files as File;
use Files\Entity\FileVersion as Version;
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
        if ($allVersions = $this->params()->fromHeader('All-Versions', false)) {
            $allVersions = (bool)$allVersions->getFieldValue();
        }

        $data = [];
        $onlyNames = false;
        $tree = false;
        $task = str_replace(['only-names', 'onlynames'], '', strtolower($task), $countReplace);
        if ($countReplace == 1) {
            $onlyNames = true;
            if (substr($task, -1) == '-') {
                $task = substr($task, 0, -1);
            }
        } else {
            if (strstr($task, "tree")) {
                $task = substr($task, 0, -5);
                $tree = true;
            }
        }

        switch ($task) {
            case "add":
                $data = $request->getPost()->toArray();
                /* @var $detail \Nomenclature\Entity\Details */
                $detail = new \Nomenclature\Entity\Details();
                $detail->setOrderId($data['orderId']);
                $detail->setGroup((isset($data['group']) && $data['group'] != null) ? $data['group'] : null);
                $detail->setCode($data['code']);
                $detail->setName($data['name']);
                $detail->setDateCreation(new \DateTime($data['dateCreation']));
                $detail->setDateEnd((isset($data['dateEnd']) && $data['dateEnd'] != null) ? new \DateTime($data['dateEnd']) : null);
                $id = true;
            case "update":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $form = new Form('detailUpload');
                    $data = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
                    if (isset($data[Form::MODELS])) {
                        foreach ($data[Form::MODELS] as $key => $model) {
                            $data[Form::MODELS][$key]['id'] = $key;
                        }
                        $data[Form::MODELS] = array_values($data[Form::MODELS]);
                    }
                    if (isset($data[Form::PROJECTS])) {
                        foreach ($data[Form::PROJECTS] as $key => $project) {
                            $data[Form::PROJECTS][$key]['id'] = $key;
                        }
                        $data[Form::PROJECTS] = array_values($data[Form::PROJECTS]);
                    }

                    if (isset($data['new'][Form::MODELS])) {
                        foreach ($data['new'][Form::MODELS] as $model) {
                            $model['id'] = null;
                            $data[Form::MODELS][] = $model;
                        }
                    }

                    if (isset($data['new'][Form::PROJECTS])) {
                        foreach ($data['new'][Form::PROJECTS] as $project) {
                            $project['id'] = null;
                            $data[Form::PROJECTS][] = $project;
                        }
                    }
                    unset($data['new']);

                    if (!isset($detail)) {
                        $detail = $this->entityManager->getRepository('Nomenclature\Entity\Details')->find($id);
                        $detail->setOrderId($data['orderId']);
                        $detail->setGroup((isset($data['group']) && $data['group'] != null) ? $data['group'] : null);
                        $detail->setCode($data['code']);
                        $detail->setName($data['name']);
                        $detail->setDateCreation(new \DateTime($data['dateCreation']));
                        $detail->setDateEnd((isset($data['dateEnd']) && $data['dateEnd'] != null) ? new \DateTime($data['dateEnd']) : null);
                    }

                    $flush = [];
                    $newCollectionId = $this->plugin('FilesPlugin')->getLastCollectionId() + 1;

                    try {
                        $form->setData($data);
                        $formData = null;
                        if ($form->isValid()) {
                            $formData = $form->getData();
                            if (isset($formData['models']) && count($formData['models'])) {
                                if ($detail->getModel()) {
                                    foreach ($formData['models'] as $fileData) {
                                        $fName = pathinfo($fileData['tmp_name'])['basename'];

                                        if ($fileData['id']) {
                                            $file = $this->entityManager->getRepository('Files\Entity\Files')->find($fileData['id']);

                                            $versions = $this->entityManager->getRepository('Files\Entity\FileVersion')->findByFileId($fileData['id'], ['date' => 'ASC']);
                                            $dropVersions = [];
                                            while (count($versions) > Version::VERSIONS_MAX_COUNT - 1) {
                                                $dropVersions[] = array_shift($versions)->getId();
                                            }
                                            if (count($dropVersions)) {
                                                $this->plugin('FilesPlugin')->dropVersions($dropVersions);
                                            }

                                            $version = new Version();
                                            $version->setFileId($file->getId());
                                            $version->setPath($fName);
                                            $flush[] = $version;
                                        } else {
                                            $file = new File();
                                            $file->setName($detail->getCode() . '.' . pathinfo($fileData['name'])['extension']);
                                            $this->entityManager->persist($file);
                                            $this->entityManager->flush();

                                            $version = new Version();
                                            $version->setFileId($file->getId());
                                            $version->setPath($fName);
                                            $flush[] = $version;

                                            $collection = new Collection();
                                            $collection->setId($detail->getModel());
                                            $collection->setFileId($file->getId());
                                            $flush[] = $collection;
                                        }
                                    }
                                } else {
                                    foreach ($formData['models'] as $fileData) {
                                        $fName = pathinfo($fileData['tmp_name'])['basename'];

                                        $file = new File();
                                        $file->setName($detail->getCode() . '.' . pathinfo($fileData['name'])['extension']);
                                        $this->entityManager->persist($file);
                                        $this->entityManager->flush();

                                        $version = new Version();
                                        $version->setFileId($file->getId());
                                        $version->setPath($fName);
                                        $flush[] = $version;

                                        $collection = new Collection();
                                        $collection->setId($newCollectionId);
                                        $collection->setFileId($file->getId());
                                        $flush[] = $collection;
                                    }
                                    $detail->setModel($newCollectionId);
                                    $newCollectionId++;
                                }
                            }
                            if (isset($formData['projects']) && count($formData['projects'])) {
                                if ($detail->getProject()) {
                                    foreach ($formData['projects'] as $fileData) {
                                        $fName = pathinfo($fileData['tmp_name'])['basename'];

                                        if ($fileData['id']) {
                                            $file = $this->entityManager->getRepository('Files\Entity\Files')->find($fileData['id']);

                                            $versions = $this->entityManager->getRepository('Files\Entity\FileVersion')->findByFileId($fileData['id'], ['date' => 'ASC']);
                                            $dropVersions = [];
                                            while (count($versions) > Version::VERSIONS_MAX_COUNT - 1) {
                                                $dropVersions[] = array_shift($versions)->getId();
                                            }
                                            if (count($dropVersions)) {
                                                $this->plugin('FilesPlugin')->dropVersions($dropVersions);
                                            }

                                            $version = new Version();
                                            $version->setFileId($file->getId());
                                            $version->setPath($fName);
                                            $flush[] = $version;
                                        } else {
                                            $file = new File();
                                            $file->setName($detail->getCode() . '.' . pathinfo($fileData['name'])['extension']);
                                            $this->entityManager->persist($file);
                                            $this->entityManager->flush();

                                            $version = new Version();
                                            $version->setFileId($file->getId());
                                            $version->setPath($fName);
                                            $flush[] = $version;

                                            $collection = new Collection();
                                            $collection->setId($detail->getProject());
                                            $collection->setFileId($file->getId());
                                            $flush[] = $collection;
                                        }
                                    }
                                } else {
                                    foreach ($formData['projects'] as $fileData) {
                                        $fName = pathinfo($fileData['tmp_name'])['basename'];

                                        $file = new File();
                                        $file->setName($detail->getCode() . '.' . pathinfo($fileData['name'])['extension']);
                                        $this->entityManager->persist($file);
                                        $this->entityManager->flush();

                                        $version = new Version();
                                        $version->setFileId($file->getId());
                                        $version->setPath($fName);
                                        $flush[] = $version;

                                        $collection = new Collection();
                                        $collection->setId($newCollectionId);
                                        $collection->setFileId($file->getId());
                                        $flush[] = $collection;
                                    }
                                    $detail->setProject($newCollectionId);
                                    $newCollectionId++;
                                }
                            }
                        }
                        if ($detail->getPattern()) {
                            if (isset($data['patterns']['new'])) {
                                if(count($data['patterns']['new'])) {
                                    foreach ($data['patterns']['new'] as $base64) {
                                        $fName = tempnam(File::UPLOAD_DIR, "img_");
                                        $fFile = fopen($fName, "wb");
                                        $base64 = explode(',', $base64);
                                        fwrite($fFile, base64_decode($base64[1]));
                                        fclose($fFile);

                                        /* @var $imagePlugin \ImagePlugin */
                                        $imagePlugin = $this->getServiceLocator()->get('imagePlugin');
                                        $imagePlugin->imgResize($fName, $fName . '.h180', 180, true);
                                        $imagePlugin->imgResize($fName, $fName . '.h950', 950, true);

                                        $fInfo = finfo_open(FILEINFO_CONTINUE);
                                        $ext = finfo_file($fInfo, $fName);
                                        finfo_close($fInfo);
                                        if (strpos($ext, ' ') !== false) {
                                            $ext = strtolower(substr($ext, 0, strpos($ext, ' ')));
                                        } else {
                                            $ext = substr(mime_content_type($fName), strpos(mime_content_type($fName), '/') + 1);
                                        }
                                        $fName = pathinfo($fName)['basename'];

                                        $file = new File();
                                        $file->setName($detail->getCode() . '.' . $ext);
                                        $this->entityManager->persist($file);
                                        $this->entityManager->flush();

                                        $version = new Version();
                                        $version->setFileId($file->getId());
                                        $version->setPath($fName);
                                        $flush[] = $version;

                                        $collection = new Collection();
                                        $collection->setId($detail->getPattern());
                                        $collection->setFileId($file->getId());
                                        $flush[] = $collection;
                                    }
                                }
                                unset($data['patterns']['new']);
                            }
                            if (isset($data['patterns']) && count($data['patterns'])) {
                                foreach ($data['patterns'] as $id => $base64) {
                                    $fName = tempnam(File::UPLOAD_DIR, "img_");
                                    $fFile = fopen($fName, "wb");
                                    $base64 = explode(',', $base64);
                                    fwrite($fFile, base64_decode($base64[1]));
                                    fclose($fFile);

                                    /* @var $imagePlugin \ImagePlugin */
                                    $imagePlugin = $this->getServiceLocator()->get('imagePlugin');
                                    $imagePlugin->imgResize($fName, $fName . '.h180', 180, true);
                                    $imagePlugin->imgResize($fName, $fName . '.h950', 950, true);

                                    $fName = pathinfo($fName)['basename'];

                                    $file = $this->entityManager->getRepository('Files\Entity\Files')->find($id);

                                    $versions = $this->entityManager->getRepository('Files\Entity\FileVersion')->findByFileId($id, ['date' => 'ASC']);
                                    $dropVersions = [];
                                    while (count($versions) > Version::VERSIONS_MAX_COUNT - 1) {
                                        $dropVersions[] = array_shift($versions)->getId();
                                    }
                                    if (count($dropVersions)) {
                                        $this->plugin('FilesPlugin')->dropVersions($dropVersions);
                                    }

                                    $version = new Version();
                                    $version->setFileId($file->getId());
                                    $version->setPath($fName);
                                    $flush[] = $version;
                                }
                            }
                        } else {
                            if (isset($data['patterns']['new']) && count($data['patterns']['new'])) {
                                foreach ($data['patterns']['new'] as $base64) {
                                    $fName = tempnam(File::UPLOAD_DIR, "img_");
                                    $fFile = fopen($fName, "wb");
                                    $base64 = explode(',', $base64);
                                    fwrite($fFile, base64_decode($base64[1]));
                                    fclose($fFile);

                                    /* @var $imagePlugin \ImagePlugin */
                                    $imagePlugin = $this->getServiceLocator()->get('imagePlugin');
                                    $imagePlugin->imgResize($fName, $fName . '.h180', 180, true);
                                    $imagePlugin->imgResize($fName, $fName . '.h950', 950, true);

                                    $fInfo = finfo_open(FILEINFO_CONTINUE);
                                    $ext = finfo_file($fInfo, $fName);
                                    finfo_close($fInfo);
                                    if (strpos($ext, ' ') !== false) {
                                        $ext = strtolower(substr($ext, 0, strpos($ext, ' ')));
                                    } else {
                                        $ext = substr(mime_content_type($fName), strpos(mime_content_type($fName), '/') + 1);
                                    }
                                    $fName = pathinfo($fName)['basename'];

                                    $file = new File();
                                    $file->setName($detail->getCode() . '.' . $ext);
                                    $this->entityManager->persist($file);
                                    $this->entityManager->flush();

                                    $version = new Version();
                                    $version->setFileId($file->getId());
                                    $version->setPath($fName);
                                    $flush[] = $version;

                                    $collection = new Collection();
                                    $collection->setId($newCollectionId);
                                    $collection->setFileId($file->getId());
                                    $flush[] = $collection;
                                }
                                $detail->setPattern($newCollectionId);
                                $newCollectionId++;
                            }
                        }

                        foreach ($flush as $item) {
                            $this->entityManager->persist($item);
                        }
                        $this->entityManager->persist($detail);
                        $this->entityManager->flush();

                        $notDoneDetail = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findOneBy(['orderId' => $detail->getOrderId(), 'dateEnd' => null]);
                        if ($notDoneDetail == null) {
                            /* @var $lastDoneDetail \Nomenclature\Entity\Details */
                            $lastDoneDetail = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findOneBy(['orderId' => $detail->getOrderId()], ['dateEnd' => 'DESC']);
                            /* @var $order \Orders\Entity\Orders */
                            $order = $this->entityManager->getRepository('Orders\Entity\Orders')->find($detail->getOrderId());
                            $order->setDateEnd($lastDoneDetail->getDateEnd());
                            $order->setStatus(\Orders\Entity\Orders::STATUS_DONE);
                            $this->entityManager->persist($order);
                            $this->entityManager->flush();
                        }

                        return new JsonModel(['error' => false, 'id' => $detail->getId()]);
                    } catch (\Exception $e) {
                        return new JsonModel(['error' => true, 'message' => $e->getMessage()]);
                    }
                }
                break;
            case "get-only-parents": case "getonlyparents": case "getOnlyParents":
                $tree = false;
                $data = null;
                $opts = ['withOrders' => true, 'withFiles' => false];
                $result['clients'] = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->findBy([], ['name' => 'ASC']), $opts);
            break;
            case "get-with-parents": case "getwithparents": case "getWithParents":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $tree = false;
                    $data = $this->entityManager->getRepository('Nomenclature\Entity\DetailsView')->find($id);
                    $opts = ['withOrders' => true, 'withFiles' => false];
                    $result['clients'] = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->findBy([], ['name' => 'ASC']), $opts);
                }
                break;
            case "get":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $data = $this->entityManager->getRepository('Nomenclature\Entity\DetailsView')->find($id);
                }
                break;
            case "get-by-order": case "getbyorder": case "getByOrder":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $result['order'] = $this->entityManager->getRepository('Orders\Entity\Orders')->find($id)->toArray();
                    $data = $this->entityManager->getRepository('Nomenclature\Entity\DetailsView')->findByOrderId($id, ['dateCreation' => 'DESC']);
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Nomenclature\Entity\DetailsView')->findBy([], ['dateCreation' => 'DESC']);
                break;
        }

        if ($tree) {
            $details = $this->plugin('DetailsPlugin')->toArray($data, ['allVersions' => $allVersions]);
            $data = [];
            $id = 0;
            foreach ($details as $item) {
                $group = $item['orderId'] . '-' . $item['group'];
                $item['status'] = $item['dateEnd'] != null;
                if ($item['group']) {
                    if (!isset($data[$group])) {
                        $data[$group] = [
                            'treeId' => $id++,
                            'status' => $item['status'],
                            'name' => $item['group'],
                            'orderCode' => $item['orderCode'],
                            'dateCreation' => $item['dateCreation'],
                            'dateEnd' => $item['dateEnd'],
                        ];

                    }
                    $item['treeId'] = $id++;
                    if (isset($data[$group]['dateCreation']) && new \DateTime($data[$group]['dateCreation']) > new \DateTime($item['dateCreation'])) {
                        $data[$group]['dateCreation'] = $item['dateCreation'];
                    }
                    if (isset($data[$group]['dateEnd']) && new \DateTime($data[$group]['dateEnd']) < new \DateTime($item['dateEnd'])) {
                        $data[$group]['dateEnd'] = $item['dateEnd'];
                    }
                    if (!$item['status']) {
                        $data[$group]['status'] = false;
                    }
                    unset($item['group']);
                    $data[$group]['__expanded__'] = false;
                    $data[$group]['__children__'][] = $item;
                } else {
                    $item['treeId'] = $id++;
                    unset($item['group']);
                    $data[] = $item;
                }
            }
            $result['data'] = array_values($data);
        } else {
            $result['data'] = $this->plugin('DetailsPlugin')->toArray($data, ['allVersions' => $allVersions, 'onlyNames' => $onlyNames]);
        }

        if (!$onlyNames) {
            $groups = $this->entityManager->getRepository('Nomenclature\Entity\Groups')->findAll();
            foreach ($groups as $key => $group) {
                $groups[$key] = $group->toArray()['group'];
            }
            $result['groups'] = $groups;
        }

        if ($error) {
            $result = ["error" => $error];
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}