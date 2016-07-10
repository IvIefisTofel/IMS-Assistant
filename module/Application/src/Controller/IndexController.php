<?php

namespace Application\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }
        return new ViewModel();
    }
}