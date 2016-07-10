<?php

namespace Fields\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if($request->isPost()) {
            $data = $request->getPost();
            $formResult = 'success';
            $formMessage = '';

            if (isset($data['form-type'])) {
                switch ($data['form-type']) {
                    case 'feedback-form': {
                        if (!isset($data['feedback-name']) || !isset($data['feedback-email']) || !isset($data['feedback-phone']) || !isset($data['feedback-message']) ||
                            $data['feedback-name'] == null || $data['feedback-email'] == null || $data['feedback-phone'] == null || $data['feedback-message'] == null
                        ) {
                            $formResult = 'error';
                            $formMessage = '&message=Поля "Имя", "Email", "Телефон" и "Текст сообщения" обязательны к заполнению.';
                        } else {
                            $message = new \Emails\Entity\Email();
                            $message->setFromUrl(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
                            $message->setTypeId(\Emails\Entity\Email::TYPE_FEEDBACK);
                            $message->setFlags([\Emails\Entity\Email::TYPE_FEEDBACK => true]);
                            $message->setSenderName($data['feedback-name']);
                            $message->setSenderEmail($data['feedback-email']);
                            if (isset($data['feedback-subject']))
                                $message->setSubject($data['feedback-subject']);

                            $phoneFormat = $this->getServiceLocator()->get('ViewHelperManager')->get('phoneFormat');
                            $msg = $data['feedback-message'] . "\n\n----------------------------\n" .
                                "Email: <a href=\"mailto:" . $data['feedback-email'] . "\">" . $data['feedback-email'] . "</a>\n" .
                                "Телефон: <a href=\"tel:" . $phoneFormat($data['feedback-phone'], true) . "\">" .
                                $phoneFormat($data['feedback-phone']) . "</a>";
                            $message->setMessage($msg);

                            $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
                            $om->persist($message);
                            $om->flush();
                        }
                        break;
                    }
                    default: {
                        $formResult = 'error';
                        $formMessage = '&message=Неизвестный тип формы.';
                        break;
                    }
                }
            } else {
                $formResult = 'error';
                $formMessage = '&message=Неизвестный тип формы.';
            }

            if (isset($_SERVER['HTTP_REFERER'])) {
                return $this->redirect()->toUrl($_SERVER['HTTP_REFERER'] . '?formresult=' . $formResult . $formMessage);
            }
            else {
                return $this->redirect()->toRoute('home');
            }
        } else {
            $this->getResponse()->setStatusCode(404);
            return new ViewModel();
        }
    }
}