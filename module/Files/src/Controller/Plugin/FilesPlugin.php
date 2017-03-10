<?php

namespace Files\Controller\Plugin;

use MCms\Controller\Plugin\MCmsPlugin;

class FilesPlugin extends MCmsPlugin
{
    public function getFiles($collections = null, $allVersions = false)
    {
        if ($collections !== null) {
            $result = [];

            if (!is_array($collections)) {
                $collections = [$collections];
            }

            $arrFiles = [];
            foreach ($this->entityManager->getRepository('Files\Entity\FilesCollections')->findById($collections) as $key => $file) {
                /* @var $file \Files\Entity\FilesCollections */
                $result[$file->getId()][$file->getFileId()] = [
                    'id' => $file->getFileId(),
                    'name' => $file->getName(),
                ];
                $arrFiles[] = $file->getFileId();
            }

            $fileVersions = [];
            foreach ($this->entityManager->getRepository('Files\Entity\FileVersion')->findByFileId($arrFiles, ['date' => 'DESC']) as $version) {
                /* @var $version \Files\Entity\FileVersion */
                $fileVersions[$version->getFileId()][$version->getId()] = [
                    'id' => $version->getId(),
//                    'date' => $version->getDateFormat('d.m.Y H:i:s'),
                    'date' => $version->getDateFormat('d.m.Y'),
                ];
            }

            foreach ($result as $key => $collection) {
                foreach ($collection as $fileId => $file) {
                    if ($allVersions) {
                        $result[$key][$fileId]['versions'] = array_values($fileVersions[$fileId]);
                    } else {
                        $lastFile = array_values($fileVersions[$fileId])[0];
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

    public function getLastCollectionId()
    {
        $sql = "SELECT DISTINCT id FROM `ims_files_collections` ORDER BY id DESC LIMIT 1";

        /* @var $stmt \Doctrine\DBAL\Statement */
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->execute();
        $result = count($res = $stmt->fetchAll()) ? (int)$res[0]['id'] : 0;

        return $result;
    }

    public function dropVersions($versionIds = [])
    {
        $versions = $this->entityManager->getRepository('Files\Entity\FileVersion')->findById($versionIds);
        /* @var $version \Files\Entity\FileVersion */
        foreach ($versions as $version) {
            if (file_exists($version->getPath())) {
                unlink($version->getPath());
            }
            if (file_exists($version->getPath() . '.h180')) {
                unlink($version->getPath() . '.h180');
            }
            if (file_exists($version->getPath() . '.h950')) {
                unlink($version->getPath() . '.h950');
            }
            $this->entityManager->remove($version);
        }
        $this->entityManager->flush();
    }
}