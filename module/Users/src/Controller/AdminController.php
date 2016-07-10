<?php
namespace Users\Controller;

use MCms\Controller\MCmsController;
use Zend\View\Model\ViewModel;
use Admin\Form\EditUser;

class AdminController extends MCmsController
{
    public function indexAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $users = $om->getRepository('Users\Entity\User')->findAll();

        return new ViewModel([
            'users' => $users,
        ]);
    }

    public function editUserAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $this->identity();
        /* @var $user \Users\Entity\User */

        $form = new EditUser([], true);

        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $valid = true;
                if (isset($data[EditUser::CONFIRM_PASSWORD]) && ($data[EditUser::PASSWORD] !=
                        $data[EditUser::CONFIRM_PASSWORD])) {
                    $form->get(EditUser::CONFIRM_PASSWORD)->setMessages(['Значения не совпдают']);
                    $valid = false;
                    if ((isset($data[EditUser::OLD_PASSWORD]) && md5($data[EditUser::OLD_PASSWORD]) !=
                            $this->identity()->getUserPassword()) && ($data[EditUser::OLD_PASSWORD] != null ||
                            $data[EditUser::PASSWORD] != null || $data[EditUser::CONFIRM_PASSWORD] != null)) {
                        $form->get(EditUser::OLD_PASSWORD)->setMessages(['Не верный пароль']);
                        $valid = false;
                    }
                }
                if ($valid) {
                    foreach ($data as $key => $val) {
                        if ($val != '' && strpos($key, 'setUser') !== false) {
                            $user->$key($val);
                            var_dump($val);
                        }
                    }
                    $om->persist($user);
                    $om->flush();
                    return $this->redirect()->toRoute('admin-users');
                }
            }
        } else {
            $form->get(EditUser::NAME)->setValue($user->getUserFullName());
            $form->get(EditUser::LOGIN)->setValue($user->getUserName());
            $form->get(EditUser::EMAIL)->setValue($user->getUserEmail());
        }

        $view =  new ViewModel([
            'edituser' => true,
            'form' => $form,
        ]);
        $view->setTemplate('users/admin/editprofile');

        return $view;
    }

    public function addProfileAction()
    {
        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = new \Users\Entity\User();

        $form = new EditUser([], $user == $this->identity(), true);

        $request = $this->getRequest();
        if($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                foreach ($data as $key => $val) {
                    if ($val != '' && strpos($key, 'setUser') !== false) {
                        $user->$key($val);
                        var_dump($val);
                    }
                }
                $om->persist($user);
                $om->flush();
                return $this->redirect()->toRoute('admin-users');
            }
        }

        $view =  new ViewModel([
            'adduser' => true,
            'form' => $form,
        ]);
        $view->setTemplate('users/admin/editprofile');

        return $view;
    }

    public function editProfileAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $om->getRepository('Users\Entity\User')->find($id);
        /* @var $user \Users\Entity\User */

        if ($user) {
            $form = new EditUser([], $user == $this->identity());

            $request = $this->getRequest();
            if($request->isPost()) {
                $form->setData($request->getPost());

                if ($form->isValid()) {
                    $data = $form->getData();
                    $valid = true;
                    if (isset($data[EditUser::CONFIRM_PASSWORD]) && ($data[EditUser::PASSWORD] !=
                            $data[EditUser::CONFIRM_PASSWORD])) {
                        $form->get(EditUser::CONFIRM_PASSWORD)->setMessages(['Значения не совпдают']);
                        $valid = false;
                        if ((isset($data[EditUser::OLD_PASSWORD]) && md5($data[EditUser::OLD_PASSWORD]) !=
                                $this->identity()->getUserPassword()) && ($data[EditUser::OLD_PASSWORD] != null ||
                                $data[EditUser::PASSWORD] != null || $data[EditUser::CONFIRM_PASSWORD] != null)) {
                            $form->get(EditUser::OLD_PASSWORD)->setMessages(['Не верный пароль']);
                            $valid = false;
                        }
                    }
                    if ($valid) {
                        foreach ($data as $key => $val) {
                            if ($val != '' && strpos($key, 'setUser') !== false) {
                                $user->$key($val);
                                var_dump($val);
                            }
                        }
                        $om->persist($user);
                        $om->flush();
                        return $this->redirect()->toRoute('admin-users');
                    }
                }
            } else {
                if ($user) {
                    $form->get(EditUser::NAME)->setValue($user->getUserFullName());
                    $form->get(EditUser::LOGIN)->setValue($user->getUserName());
                    $form->get(EditUser::EMAIL)->setValue($user->getUserEmail());
                    if ($user != $this->identity()) {
                        $form->get(EditUser::ROLE)->setValue($user->getUserRoleID());
                        $form->get(EditUser::ACTIVE)->setValue($user->getUserActive());
                    }
                }
            }
        } else
            return $this->redirect()->toRoute('admin-users');

        return new ViewModel([
            'form' => $form,
        ]);
    }

    public function deleteProfileAction()
    {
        $id = $this->params()->fromRoute('id', 0);

        $om = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        $user = $om->getRepository('Users\Entity\User')->find($id);

        if ($user && $user != $this->identity()) {
            $om->remove($user);
            $om->flush();
        }

        return $this->redirect()->toRoute('admin-users');
    }
}