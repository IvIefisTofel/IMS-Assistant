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
//            $error = 404;
        }

        $task   = $this->params()->fromRoute('task', null);
        $id     = $this->params()->fromRoute('id', null);

        $client = null;
        $data = [];

        switch ($task) {
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $data = $this->entityManager->getRepository('Orders\Entity\Orders')->find($id);
                    if (count($data) > 0) {
                        $data = $data->toArray();
                    }
                }
                break;
            case 'getByClient' || "get-by-client" || 'getbyclient':
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $client = $this->entityManager->getRepository('Clients\Entity\Clients')->find($id)->toArray();
                    $data = $this->entityManager->getRepository('Orders\Entity\Orders')->findBy(['client' => $id]);
                    if (count($data) > 0) {
                        foreach ($data as $key => $val) {
                            $data[$key] = $val->toArray();
                        }
                    }
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Orders\Entity\Orders')->findBy([], ['dateCreation' => 'DESC']);
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
                "client" => $client,
            ]);
        }
    }
}