<?php

namespace Clients\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;

class IndexController extends MCmsController
{
    public function indexAction()
    {
//        /** @var \Clients\Entity\Clients $client */
//        $client = $this->entityManager->getRepository('Clients\Entity\Clients')->findOneBy([]);
//        /** @var \Orders\Entity\Orders $order */
//        $order = $client->getOrders()->first();
//        /** @var \Nomenclature\Entity\Details $detail */
//        $detail = $order->getDetails()->first();
//        /** @var \Files\Entity\Files $file */
//        $file = $detail->getPattern();
//        $versions = $file->getVersions();
//        /** @var \Files\Entity\FileVersion $version */
//        $version = $versions->first();
//        return new JsonModel($file->toArray());
//        exit;
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

        $data = [];

        switch ($task) {
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $data = $this->entityManager->getRepository('Clients\Entity\Clients')->find($id);
                    if (count($data) > 0) {
                        $data = $data->toArray();
                    } else {
                        $error = "No clients found.";
                    }
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Clients\Entity\Clients')->findBy([], ['name' => 'ASC']);
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
            ]);
        }
    }
}