<?php

namespace Orders\Controller;

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

        $task   = $this->params()->fromRoute('task', null);
        $id     = $this->params()->fromRoute('id', null);


        $clientName = null;
        $data = [];
        switch ($task) {
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('\Orders\Entity\Orders')->find($id));
                }
                break;
            case 'getByClient' || "get-by-client" || 'getbyclient':
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $clientName = $this->entityManager->getRepository('\Clients\Entity\Clients')->findOneById($id)->getName();
                    $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('Orders\Entity\Orders')->findByClientId($id));
                }
                break;
            default:
                $data = $this->plugin('OrdersPlugin')->toArray($this->entityManager->getRepository('Orders\Entity\Orders')->findBy([], ['dateCreation' => 'DESC']));
                break;
        }

        if ($error) {
            return new JsonModel(["error" => $error]);
        } else {
            return new JsonModel([
                "data" => $data,
                "clientName" => $clientName,
            ]);
        }
    }
}