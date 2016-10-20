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

        $data = [];
        $order = null;
        $tree = false;
        if (strstr($task, "tree")) {
            $task = substr($task, 0, -5);
            $tree = true;
        }

        switch ($task) {
            case "get":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
//                    $data = $this->entityManager->getRepository('Nomenclature\Entity\Details')->find($id);
                    /**
                     * @todo Убрать
                     */
                    var_dump($this->plugin('DetailsPlugin')->toArray($this->entityManager->getRepository('Nomenclature\Entity\Details')->find($id)));
                    exit;
                }
                break;
            case "getByOrder": case "get-by-order": case "getbyorder":
                if ($id === null)
                    $error = "Error: id is not valid!";
                else {
                    $order = $this->entityManager->getRepository('Orders\Entity\Orders')->find($id)->toArray();
                    $data = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findByOrderId($id, ['dateCreation' => 'DESC']);
                }
                break;
            default:
                $data = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findBy([], ['dateCreation' => 'DESC']);
                break;
        }

        if ($tree) {
            $details = $this->plugin('DetailsPlugin')->toArray($data);
            $data = [];
            $id = 0;
            foreach ($details as $item) {
                $group = $item['orderId'] . '-' . $item['group'];
                $item['status'] = $item['dateEnd'] != null;
                if ($item['group']) {
                    if (!isset($data[$group])) {
                        $data[$group] = [
                            'treeId' => $id++,
                            'status' => $item['status'],
                            'name' => $item['group'],
                            'orderCode' => $item['orderCode'],
                            'dateCreation' => $item['dateCreation'],
                            'dateEnd' => $item['dateEnd'],
                        ];

                    }
                    $item['treeId'] = $id++;
                    if (isset($data[$group]['dateCreation']) && new \DateTime($data[$group]['dateCreation']) > new \DateTime($item['dateCreation'])) {
                        $data[$group]['dateCreation'] = $item['dateCreation'];
                    }
                    if (isset($data[$group]['dateEnd']) && new \DateTime($data[$group]['dateEnd']) < new \DateTime($item['dateEnd'])) {
                        $data[$group]['dateEnd'] = $item['dateEnd'];
                    }
                    if (!$item['status']) {
                        $data[$group]['status'] = false;
                    }
                    unset($item['group']);
                    $data[$group]['__expanded__'] = false;
                    $data[$group]['__children__'][] = $item;
                } else {
                    $item['treeId'] = $id++;
                    unset($item['group']);
                    $data[] = $item;
                }
            }
            $data = array_values($data);
        } else {
            $data = $this->plugin('DetailsPlugin')->toArray($data);
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