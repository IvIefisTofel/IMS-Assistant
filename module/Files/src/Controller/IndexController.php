<?php
namespace Files\Controller;

use MCms\Controller\MCmsController;
use Assetic\Asset\FileAsset;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $repository = $this->entityManager->getRepository('Fields\Entity\FieldsValues');
        /* @var $field \Fields\Entity\FieldsValues */
        $field = $repository->findOneByAlias($this->params()->fromRoute('alias'));

        if ($field != null) {
            $file = new FileAsset($field->getValue());
            $file->load();

            header('Content-Type: ' . mime_content_type($field->getValue()));
            echo $file->dump();
            exit;
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }
}