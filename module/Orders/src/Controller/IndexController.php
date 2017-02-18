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
        switch ($task) {
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

        if ($error) {
            $result = ["error" => $error];
        } else {
            $result = [
                "data" => $data,
                "clientName" => $clientName,
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