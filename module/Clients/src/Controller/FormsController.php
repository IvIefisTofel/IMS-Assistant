<?php

namespace Clients\Controller;

use Files\Entity\FileVersions;
use MCms\Controller\MCmsController;
use Zend\View\Model\JsonModel;

class FormsController extends MCmsController
{
    public function indexAction()
    {
        $this->getResponse()->setStatusCode(404);
        return;
    }

    public function addAction()
    {
        $form = new \Clients\Form\AddForm();

        /*$viewModel = new ViewModel([
            'form' => $form,
        ]);
        $viewModel->setTerminal(true);

        return $viewModel;*/

        $request = $this->getRequest();
        if($request->isPost()){ //&& $request->isXmlHttpRequest()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $formData = $form->getData();

                $files = [];
                foreach ($formData[\Clients\Form\AddForm::ADDITIONS] as $formFile) {
                    $file = $this->plugin('FilesPlugin')->addFile($formFile['name'], $formFile['tmp_name']);

                    $files[] = $file->toArray();
                }
                $this->entityManager->flush();

                var_dump($files);
                exit;

            } else {
                return new JsonModel([
                    'error' => true,
                ]);
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}