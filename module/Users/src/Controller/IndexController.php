<?php

namespace Users\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;
use Users\Entity\Users;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $error = false;
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $task   = $this->params()->fromRoute('task', null);
        $id     = $this->params()->fromRoute('id', null);

        $data = [];
        /* @var $user Users */
        switch ($task) {
            case 'add':
                $user = new Users();
                goto user;
            case 'edit':
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $user = $this->entityManager->getRepository(Users::class)->find($id);
                    user:
                    if ($user) {
                        $postData = $request->getPost()->toArray();
                        $edited = false;

                        if (!isset($postData['password']) || !$this->identity()->validPassword($postData['password'])) {
                            $error = true;
                            $messages['password'] = 'Не верный пароль';
                        } else {
                            if ($postData['name'] != $user->getName()) {
                                $user->setName($postData['name']);
                                $edited = true;
                            }
                            if ($postData['fullName'] != $user->getFullName()) {
                                $user->setFullName($postData['fullName']);
                                $edited = true;
                            }
                            if ($postData['email'] != $user->getEmail()) {
                                $user->setEmail($postData['email']);
                                $edited = true;
                            }
                            if (isset($postData['newPassword'])) {
                                $confirmPassword = isset($postData['confirmPassword']) ? $postData['confirmPassword'] : null;
                                if ($postData['newPassword'] != $confirmPassword) {
                                    $error = true;
                                    $messages['newPassword'] = 'Пароли не совпадают.';
                                } else {
                                    $user->setPassword($postData['newPassword']);
                                    $edited = true;
                                }
                            }
                            if ($user->getId() != $this->identity()->getId() && $postData['roleId'] != $user->getRoleID()) {
                                if ($user->setRoleID($postData['roleId'])) {
                                    $edited = true;
                                }
                            }
                            if ($postData['active'] != $user->getActive()) {
                                $user->setActive($postData['active']);
                                $edited = true;
                            }
                        }

                        if (!$error && !isset($messages) && $edited) {
                            $this->entityManager->persist($user);
                            $this->entityManager->flush();
                        }
                        $data = $user;
                    }
                }
                break;
            case "get":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $data = $this->entityManager->getRepository(Users::class)->find($id);
                }
                break;
            default:
                $data = $this->entityManager->getRepository(Users::class)->findBy([], ['name' => 'ASC']);
                break;
        }

        $result = [];
        if ($error) {
            $result = [
                'error' => true,
            ];
            if (isset($messages)) {
                $messages[] = $error;
                $result['messages'] = $messages;
            }
        } else {
            if (isset($data)) {
                $data = $this->plugin('users')->toArray($data);
                $result['data'] = $data;
            }
        }
        if ($dev) {
            print_r($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }

    public function nameListAction()
    {
        $error = false;
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $data = [];
        try {
            $data = $this->entityManager->getRepository(Users::class)->findBy([], ['name' => 'ASC']);
            $data = $this->plugin('users')->toArray($data, ['onlyNames' => true]);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if ($error !== false) {
            $result = [
                'error' => true,
                'message' => $error,
            ];
        } else {
            $result['data'] = $data;
        }
        if ($dev) {
            print_r($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }

    public function identityAction()
    {
        $error = false;
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $data = [];
        try {
            $data = $this->plugin('users')->toArray($data = $this->identity());
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if ($error !== false) {
            $result = [
                'error' => true,
                'message' => $error,
            ];
        } else {
            $result['data'] = array_shift($data);
        }
        if ($dev) {
            print_r($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }

    public function editIdentityAction()
    {
        return $this->forward()->dispatch('Users\Controller\Index', [
            'action' => 'index',
            'task' => 'edit',
            'id' => $this->identity()->getId(),
        ]);
    }
}