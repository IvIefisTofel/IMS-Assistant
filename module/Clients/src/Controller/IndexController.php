<?php

namespace Clients\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost()) {
//            $this->getResponse()->setStatusCode(404);
//            return;

            return new JsonModel([
                "error" => "404",
                "data" => null,
            ]);
        }
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }



        return new JsonModel([
            "error" => "false",
            "data" => null,
        ]);
    }
}