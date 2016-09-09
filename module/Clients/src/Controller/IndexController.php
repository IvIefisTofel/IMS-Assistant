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

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $task = $this->params()->fromRoute('task', null);
        $id   = $this->params()->fromRoute('id', null);

        $data = [];

        switch ($task) {
            case "get":
                if ($id === null) {
                    $error = "Error: id is not valid!";
                } else {
                    $data = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->find($id));
                }
                break;
            default:
                $data = $this->plugin('ClientsPlugin')->toArray($this->entityManager->getRepository('Clients\Entity\Clients')->findBy([], ['name' => 'ASC']));
                break;
        }

        if ($error) {
            return new JsonModel(["error" => $error]);
        } else {
            return new JsonModel($data);
        }
    }
}