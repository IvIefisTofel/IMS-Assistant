<?php

namespace MCms\Controller;

use Zend\View\Model\JsonModel;

class ErrorController extends MCmsController
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

        $task   = $this->params()->fromRoute('task', null);
        $id     = $this->params()->fromRoute('id', null);

        try {
            $data = [];
            switch ($task) {
                case "del-error": case "delerror": case "delError":
                    $drop = true;
                case "set-unread": case "setunread": case "setUnread":
                    $read = false;
                case "set-read": case "setread": case "setRead":
                    if (!isset($read)) {
                        $read = true;
                    }
                    $arrIds = [];
                    if ($id != null) {
                        $arrIds[] = $id;
                    } else {
                        $arrIds = $request->getPost()->toArray()['ids'];
                    }
                    $errors = $this->entityManager->getRepository('MCms\Entity\Errors')->findById($arrIds);
                    if (!is_array($errors)) {
                        $errors = [$errors];
                    }
                    foreach ($errors as $error) {
                        /* @var $error \MCms\Entity\Errors */
                        if (isset($drop)) {
                            $this->entityManager->remove($error);
                        } else {
                            $error->setRead($read);
                            $this->entityManager->persist($error);
                        }
                    }
                    $this->entityManager->flush();
                    break;
                case "get":
                    if ($id === null)
                        $errMsg = self::INVALID_ID;
                    else {
                        $data = $this->entityManager->getRepository('MCms\Entity\Errors')->find($id);
                        if ($data == null) {
                            $errMsg = "Error: No entries found for id $id.";
                        }
                    }
                    break;
                default:
                    $data = $this->entityManager->getRepository('MCms\Entity\Errors')->findBy([], ['date' => 'DESC']);
                    break;
            }

            if (count($data)) {
                $result['data'] = $this->plugin('ErrorsPlugin')->toArray($data);
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