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
//            $error = 404;
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
                    $data = $this->entityManager->getRepository('Nomenclature\Entity\Details')->find($id);
                    if (count($data) > 0) {
                        $data = $data->toArray();
                    }
                }
                break;
            case 'getByOrder' || "get-by-order" || 'getbyorder':
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    /* @var $order \Orders\Entity\Orders */
                    $order = $this->entityManager->getRepository('Orders\Entity\Orders')->find($id);//->toArray();
                    $data = $order->getDetails()->toArray();
                    $order = $order->toArray();
                    if (count($data) > 0) {
                        foreach ($data as $key => $val) {
                            $data[$key] = $val->toArray();
                        }
                    }
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findBy([], ['dateCreation' => 'DESC']);
                if (count($data) > 0) {
                    foreach ($data as $key => $val) {
                        $data[$key] = $val->toArray();
                    }
                }
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