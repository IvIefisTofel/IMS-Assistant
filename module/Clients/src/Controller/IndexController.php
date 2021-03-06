<?php

namespace Clients\Controller;

use AuthDoctrine\Acl\Acl;
use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;
use MCms\Entity\Events as Event;
use Clients\Form\ClientsUpload as Form;
use Clients\Entity\Clients as Client;
use Files\Entity\Collections as Collection;
use Files\Entity\Files as File;
use Files\Entity\FileVersion as Version;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $error = false;
        $dev = $this->params()->fromQuery('dev_code', null) == Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $task = $this->params()->fromRoute('task', null);
        $id   = $this->params()->fromRoute('id', null);
        $onlyNames = false;
        $task = str_replace(['only-names', 'onlynames'], '', strtolower($task), $countReplace);
        if ($countReplace == 1) {
            $onlyNames = true;
            if (substr($task, -1) == '-') {
                $task = substr($task, 0, -1);
            }
        }

        try {
            $data = [];
            $flush = [];
            $event = new Event();
            $event->setUserId($this->identity()->getId());
            switch ($task) {
                case "add":
                    if ($this->identity()->getCurrentRole() < SUPERVISOR_ROLE) {
                        $error = Acl::ACCESS_DENIED;
                        break;
                    }
                    $message = 'Клиент добавлен.';
                    $client = new Client();
                    $event->setType(Event::E_CLIENT_CREATE);
                    goto client;
                case "update":
                    if ($this->identity()->getCurrentRole() < SUPERVISOR_ROLE) {
                        $error = Acl::ACCESS_DENIED;
                        break;
                    }
                    if ($id === null)
                        $error = self::INVALID_ID;
                    else {
                        $message = 'Клиент изменен.';
                        client:
                        if ($this->identity()->getCurrentRole() < SUPERVISOR_ROLE) {
                            $error = Acl::ACCESS_DENIED;
                            break;
                        }
                        $form = new Form('clientsUpload');
                        $post = array_merge($request->getPost()->toArray(), $request->getFiles()->toArray());
                        if (isset($post[Form::ADDONS])) {
                            foreach ($post[Form::ADDONS] as $key => $addon) {
                                $post[Form::ADDONS][$key]['id'] = $key;
                            }
                            $post[Form::ADDONS] = array_values($post[Form::ADDONS]);
                        }

                        if (!isset($client)) {
                            $client = $this->entityManager->getRepository(Client::class)->find($id);
                            $event->setType(Event::E_CLIENT_UPDATE);
                        }
                        $client->setDescription(isset($post['clientDescription']) ? $post['clientDescription'] : null);

                        $form->setData($post);
                        $formData = null;
                        if ($form->isValid()) {
                            $formData = $form->getData();
                            $client->setName($formData['clientName']);
                            if (isset($formData[Form::ADDONS]) || isset($formData[Form::NEW_ADDONS])) {
                                if ($client->getAdditions() == null) {
                                    $client->setAdditions($this->plugin('files')->getLastCollectionId() + 1);
                                }

                                if (isset($formData[Form::NEW_ADDONS])) {
                                    foreach ($formData[Form::NEW_ADDONS] as $key => $addon) {
                                        $file = new File();
                                        $file->setName($addon['name']);
                                        $formData[Form::NEW_ADDONS][$key]['file'] = $file;
                                        $this->entityManager->persist($file);
                                    }
                                    $this->entityManager->flush();
                                    foreach ($formData[Form::NEW_ADDONS] as $addon) {
                                        $collection = new Collection();
                                        $collection->setId($client->getAdditions());
                                        $collection->setFileId($addon['file']->getId());
                                        $flush[] = $collection;

                                        $version = new Version();
                                        $version->setFileId($addon['file']->getId());
                                        $version->setPath(pathinfo($addon['tmp_name'])['basename']);
                                        $flush[] = $version;
                                    }
                                }

                                if (isset($formData[Form::ADDONS])) {
                                    $fileIds = [];
                                    foreach ($formData[Form::ADDONS] as $addon) {
                                        $fileIds[] = $addon['id'];

                                        // Меняет расширение файла (посчитал это нецелесообразным)
                                        //$file = $this->entityManager->getRepository(File::class)->find($addon['id']);
                                        ///* @var $file File */
                                        //$file->setName(pathinfo($file->getName())['filename'] . '.' . pathinfo($addon['name'])['extension']);
                                        //$flush[] = $file;

                                        $version = new Version();
                                        $version->setFileId($addon['id']);
                                        $version->setPath(pathinfo($addon['tmp_name'])['basename']);
                                        $flush[] = $version;
                                    }

                                    $versionsArr = $this->entityManager->getRepository(Version::class)->findByFileId($fileIds, ['date' => 'ASC']);
                                    $versionsByFiles = [];
                                    foreach ($versionsArr as $version) {
                                        /* @var $version Version */
                                        $versionsByFiles[$version->getFileId()][] = $version;
                                    }

                                    $dropVersions = [];
                                    foreach ($versionsByFiles as $key => $versionsByFile) {
                                        while (count($versionsByFile) > Version::VERSIONS_MAX_COUNT - 1) {
                                            $dropVersions[] = array_shift($versionsByFile)->getId();
                                        }
                                    }
                                    if (count($dropVersions)) {
                                        $this->plugin('files')->dropVersions($dropVersions);
                                    }
                                }
                                foreach ($flush as $item) {
                                    $this->entityManager->persist($item);
                                }
                            }
                            $this->entityManager->persist($client);
                            if ($id === true) {
                                $this->entityManager->flush();
                                $event->setEntityId($client->getId());
                                $this->entityManager->persist($event);
                            } else {
                                $event->setEntityId($client->getId());
                                $this->entityManager->persist($event);
                            }
                            $this->entityManager->flush();
                            $data = $client;
                            $status = true;
                        }
                    }
                    break;
                case "get":
                    if ($id === null) {
                        $error = self::INVALID_ID;
                    } else {
                        if ($this->identity()->getCurrentRole() < USER_ROLE) {
                            $error = Acl::ACCESS_DENIED;
                            break;
                        }
                        $data = $this->entityManager->getRepository('Clients\Entity\Clients')->find($id);
                    }
                    break;
                default:
                    $data = $this->entityManager->getRepository('Clients\Entity\Clients')->findAll();
                    break;
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $result = [];
        if ($error) {
            $result = [
                'error' => 'true',
                'message' => $error,
            ];
        } else {
            if (isset($data)) {
                $result['data'] = $data = $this->plugin('clients')->toArray($data, ['onlyNames' => $onlyNames]);;
            }
            if (isset($status) && isset($message)) {
                $result['status'] = $status;
                $result['message'] = $message;
            }
        }
        if ($dev) {
            print_r($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}