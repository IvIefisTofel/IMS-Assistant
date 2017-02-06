<?php

namespace Services\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;

class NotificationsController extends MCmsController
{
    private function getHotOrders()
    {
        $q = $this->entityManager->createQueryBuilder();
        $q->select('o')
            ->from('Orders\Entity\OrdersView', 'o')
            ->where('o.dateEnd is NULL AND o.dateDeadline > ' . date_format(new \DateTime("-10 days"), 'Y-m-d'));

        return $q->getQuery()->getResult();
    }

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

        $data = [
            'hotOrders' => $this->plugin('OrdersPlugin')->toArray(self::getHotOrders()),
        ];


        if ($error) {
            $result = ["error" => $error];
        } else {
            $result = $data;
        }
        if ($dev) {
            var_dump($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}