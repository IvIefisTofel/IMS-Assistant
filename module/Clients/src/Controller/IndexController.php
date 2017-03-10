<?php

namespace Clients\Controller;

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

        $data = [];

        switch ($task) {
            case "get":
                if ($id === null) {
                    $error = self::INVALID_ID;
                } else {
                    $data = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->find($id), ['onlyNames' => $onlyNames]);
                }
                break;
            default:
                $data = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->findBy([], ['name' => 'ASC']), ['onlyNames' => $onlyNames]);
                break;
        }

        if ($error) {
            $result= ["error" => $error];
        } else {
            $result = $data;
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}