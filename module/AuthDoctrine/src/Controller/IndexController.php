<?php
namespace AuthDoctrine\Controller;

use MCms\Controller\MCmsController;
use AuthDoctrine\Form\LoginForm;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class IndexController extends MCmsController
{
    public function indexAction()
	{
        $request = $this->getRequest();
		/* @var $user \Users\Entity\Users */
		if ($user = $this->identity() && (!$request->isXmlHttpRequest() && $request->isPost())) {
            return $this->redirect()->toRoute('home');
		}

		$authErrors = [];

		$loginForm = new LoginForm();
		$loginForm->setAttribute('action', $this->url()->fromRoute('login'));
		$users = $this->entityManager->getRepository('Users\Entity\Users')->findBy([], ['name' => 'ASC']);
        foreach ($users as $user) {
            $opts[$user->getName()] = $user->getName();
		}
		$loginForm->get(LoginForm::NAME)->setValueOptions($opts);

		if ($request->isPost()) {
			$data = $request->getPost();

			$loginForm->setData($data);
			if ($loginForm->isValid()) {
				$loginForm->getData();

				$authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
				$adapter = $authService->getAdapter();
				$adapter->setIdentity($data[LoginForm::NAME]);
				$adapter->setCredential($data[LoginForm::PASSWORD]);
				$authResult = $authService->authenticate();

				if ($authResult->isValid()) {
				    /* @var $identity \Users\Entity\Users */
					$identity = $authResult->getIdentity();
					$authService->getStorage()->write($identity);
					if ($identity->getPassword() == md5($data[LoginForm::PASSWORD])
                        && $identity->getRoleID() == \Users\Entity\Users::ADMIN_ROLE) {
					    $permissions = true;
                        setcookie('rights', 'true', time() + 60*60*24, '/', $this->getRequest()->getUri()->getHost());
                    } else {
                        $permissions = false;
                        setcookie('rights', null, time() - 3600, '/', $this->getRequest()->getUri()->getHost());
                    }
                    if ($request->isXmlHttpRequest()) {
					    return new JsonModel(['auth' => true, 'permissions' => $permissions]);
                    } else {
                        return $this->redirect()->toRoute('home');
                    }
				} else {
                    if ($request->isXmlHttpRequest()) {
                        return new JsonModel(['auth' => false]);
                    } else {
                        $authErrors['auth'] = 'Не верная комбинация логина и пароля.';
                    }
				}
			}
		}

		$view = new ViewModel(array(
			'authErrors' => $authErrors,
			'loginForm'	=> $loginForm,
		));
		$view->setTerminal(true);

		return $view;
	}
	
	public function logoutAction()
	{
		$auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
		$auth->clearIdentity();

		$sessionManager = new \Zend\Session\SessionManager();
		$sessionManager->forgetMe();
        setcookie('lockScreen', '', -3600, '/', $this->getRequest()->getUri()->getHost());
		
		return $this->redirect()->toRoute('login');
	}
}