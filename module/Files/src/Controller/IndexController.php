<?php

namespace Files\Controller;

use Assetic\Asset\FileAsset;
use MCms\Controller\MCmsController;
use Files\Entity\Files;

class IndexController extends MCmsController
{
    public function indexAction()
    {
        $aspect = $this->params()->fromRoute('aspect', null);
        $size = $this->params()->fromRoute('size', null);
        $versionId = $this->params()->fromRoute('versionId', null);
        $fileName = $this->params()->fromRoute('fileName', null);

        if ($versionId && $fileName) {
            if ($version = $this->entityManager->getRepository('Files\Entity\FileVersion')->find($versionId)) {
                /* @var $version \Files\Entity\FileVersion */
                if ($aspect && $size) {
                    $path = Files::UPLOAD_DIR . $version->getPath() . '.' . $aspect . $size;
                    if (!file_exists($path)) {
                        $path = Files::UPLOAD_DIR . $version->getPath();
                    }
                } else {
                    $path = Files::UPLOAD_DIR . $version->getPath();
                }
                if (file_exists($path)) {
                    $file = new FileAsset($path);
                    $file->load();

                    header('Content-Type: ' . mime_content_type($path));
                    echo $file->dump();
                    exit;
                }
            }
        }

        $this->getResponse()->setStatusCode(404);
        return;
    }
}