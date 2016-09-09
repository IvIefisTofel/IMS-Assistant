<?php

namespace Nomenclature\Controller;

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

        $order = null;
        $data = [];

        switch ($task) {
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $data = $this->plugin('DetailsPlugin')->toArray($this->entityManager->getRepository('Nomenclature\Entity\Details')->find($id));
                }
                break;
            case 'getByOrder' || "get-by-order" || 'getbyorder':
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $order = $this->entityManager->getRepository('Orders\Entity\Orders')->find($id)->toArray();
                    $data = $this->plugin('DetailsPlugin')->toArray($this->entityManager->getRepository('Nomenclature\Entity\Details')->findByOrderId($id, ['dateCreation' => 'DESC']));
                }
                break;
            default:
                $data = $this->plugin('DetailsPlugin')->toArray($this->entityManager->getRepository('Nomenclature\Entity\Details')->findBy([], ['dateCreation' => 'DESC']));
                break;
        }

        if ($error) {
            return new JsonModel(["error" => $error]);
        } else {
            return new JsonModel([
                "data" => $data,
                "order" => $order,
            ]);
        }
    }
}