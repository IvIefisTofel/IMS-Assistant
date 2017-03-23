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
            ->where('o.dateEnd is NULL AND (o.dateDeadline > ' . date_format(new \DateTime("-10 days"), 'Y-m-d') . ' OR o.dateDeadline < ' . date('Y-m-d') . ')');

        return $q->getQuery()->getResult();
    }

    public function indexAction()
    {
        if (!$this->identity()){
            return $this->redirect()->toRoute('login');
        }

        $error = false;
        $data = [];
        $dev = $this->params()->fromQuery('dev_code', null) == \AuthDoctrine\Acl\Acl::DEV_CODE;

        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest() && !$request->isPost() && !$dev) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        try {
            $data = [
                'hotOrders' => $this->plugin('orders')->toArray(self::getHotOrders()),
                'errors' => $this->plugin('errors')->toArray($this->entityManager->getRepository(\MCms\Entity\Errors::class)->findByRead(false, ['date' => 'DESC'])),
            ];
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        if ($error) {
            $result = ["error" => $error];
        } else {
            $result = $data;
        }
        if ($dev) {
            print_r($result);
            exit;
        } else {
            return new JsonModel($result);
        }
    }
}