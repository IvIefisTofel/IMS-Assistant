<?php
namespace AuthDoctrine\Controller;

use MCms\Controller\MCmsController;
use AuthDoctrine\Form\LoginForm;
use Zend\View\Model\ViewModel;

class IndexController extends MCmsController
{
    public function indexAction()
	{
		/* @var $user \Users\Entity\User */
		if ($user = $this->identity()) {
			if (isset($_COOKIE['lockScreen'])) {
				return $this->redirect()->toRoute('lock-screen');
			} else {
				return $this->redirect()->toRoute('admin');
			}
		}

		$authErrors = [];

		$loginForm = new LoginForm();
		$loginForm->setAttribute('action', $this->url()->fromRoute('login'));

		$request = $this->getRequest();
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
					$identity = $authResult->getIdentity();
					$authService->getStorage()->write($identity);
					//if ($data[LoginForm::REMEMBER]) {
					//	$sessionManager = new \Zend\Session\SessionManager();
					//	$time = 60 * 60 * 24 * 7; // 60(seconds)*60(minutes)*24(hours) = 86400 = 1 day
					//	$sessionManager->rememberMe($time);
					//}
					return $this->redirect()->toRoute('home');
				} else {
					$authErrors['auth'] = 'Не верная комбинация логина и пароля.';
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