<?php

namespace Users\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;

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

        $data = [];
        /* @var $user \Users\Entity\Users */
        switch ($task) {
            case "get-identity": case "getidentity": case "getIdentity":
                $data = $this->identity()->toArray();
                break;
            case "get-name-list": case "getnamelist": case "getNameList":
                $data = $this->entityManager->getRepository('Users\Entity\Users')->findBy([], ['name' => 'ASC']);
                foreach ($data as $key => $user) {
                    $data[$key] = $user->getName();
                }
                break;
            case "get":
                if ($id === null)
                    $error = self::INVALID_ID;
                else {
                    $data = $this->entityManager->getRepository('Users\Entity\Users')->find($id);
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Users\Entity\Users')->findBy([], ['name' => 'ASC']);
                break;
        }

        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as $key => $user) {
            if ($user instanceof \Users\Entity\Users) {
                $data[$key] = $user->toArray();
            }
        }

        if ($error) {
            $result = ["error" => $error];
        } else {
            $result = [
                "data" => $data,
            ];
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}