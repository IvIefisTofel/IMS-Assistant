<?php

namespace Files\Controller;

use Assetic\Asset\FileAsset;
use MCms\Controller\MCmsController;
use Files\Entity\Files;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $versionId = $this->params()->fromRoute('versionId', null);
        $fileName = $this->params()->fromRoute('fileName', null);

        if ($versionId && $fileName) {
            if ($version = $this->entityManager->getRepository('Files\Entity\FileVersion')->find($versionId)) {
                /* @var $version \Files\Entity\FileVersion */
                $file = new FileAsset(Files::UPLOAD_DIR . $version->getPath());
                $file->load();

                header('Content-Type: ' . mime_content_type(Files::UPLOAD_DIR . $version->getPath()));
                echo $file->dump();
                exit;
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}