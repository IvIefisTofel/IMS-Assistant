<?php

namespace Application\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }
        return new ViewModel();
    }

    public function permissionsAction()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost()) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $access = false;
        if ($this->identity()){
            $access = true;
        }
        return new JsonModel([
            'access' => $access,
        ]);
    }
}