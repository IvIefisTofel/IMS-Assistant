<?php

namespace Files\Controller\Plugin;

use Files\Entity\Files;
use Files\Entity\FileVersions;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class FilesPlugin extends AbstractPlugin
{
    public function getFiles($collections = null, $allVersions = false)
    {
        if ($collections !== null) {
            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $result = [];

            if (!is_array($collections)) {
                $collections = [$collections];
            }

            $arrFiles = [];
            foreach ($em->getRepository('Files\Entity\FilesCollections')->findById($collections) as $key => $file) {
                /* @var $file \Files\Entity\FilesCollections */
                $result[$file->getId()][$file->getFileId()] = [
                    'id' => $file->getFileId(),
                    'name' => $file->getName(),
                ];
                $arrFiles[] = $file->getFileId();
            }

            $fileVersions = [];
            foreach ($em->getRepository('Files\Entity\FileVersion')->findByFileId($arrFiles, ['date' => 'DESC']) as $version) {
                /* @var $version \Files\Entity\FileVersion */
                $fileVersions[$version->getFileId()][$version->getId()] = [
                    'id' => $version->getId(),
                    'date' => $version->getDateFormat('d.m.Y H:i:s'),
                ];
            }

            foreach ($result as $key => $collection) {
                foreach ($collection as $fileId => $file) {
                    if ($allVersions) {
                        $result[$key][$fileId]['versions'] = array_values($fileVersions[$fileId]);
                    } else {
                        $lastFile = array_shift($fileVersions[$fileId]);
                        $result[$key][$fileId]['id'] = $lastFile['id'];
                        $result[$key][$fileId]['date'] = $lastFile['date'];
                    }
                }

                $result[$key] = array_values($result[$key]);
            }

            return $result;
        }

        return null;
    }
}