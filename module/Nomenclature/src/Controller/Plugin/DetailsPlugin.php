<?php

namespace Nomenclature\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class DetailsPlugin extends AbstractPlugin
{
    public function toArray($details = null, $options = [])
    {
        if ($details !== null) {
            $allVersions = isset($options['allVersions']) ? $options['allVersions'] : false;

            /* @var $em \Doctrine\ORM\EntityManager */
            $em = $this->getController()->getServiceLocator()->get('Doctrine\ORM\EntityManager');
            $result = [];

            if (!is_array($details)) {
                $details = [$details];
            }

            $collections = [];
            foreach ($details as $key => $detail) {
                if (get_class($detail) == 'Nomenclature\Entity\Details') {
                    /* @var $detail \Nomenclature\Entity\Details */
                    $result[$detail->getId()] = $detail->toArray();
                    $result[$detail->getId()]['dateCreation'] = $detail->getDateCreationFormat('Y-m-d');
                    $result[$detail->getId()]['dateEnd'] = $detail->getDateEndFormat('Y-m-d');
                    if ($detail->getPattern()) {
                        $collections[$detail->getPattern()] = true;
                    }
                    if ($detail->getModel()) {
                        $collections[$detail->getModel()] = true;
                    }
                    if ($detail->getProject()) {
                        $collections[$detail->getProject()] = true;
                    }
                } else {
                    throw new \Exception('Array elements must be Nomenclature\Entity\Details class.');
                }
            }

            $collectionFiles = $this->controller->plugin('FilesPlugin')->getFiles(array_keys($collections), $allVersions);

            foreach ($result as $key => $detail) {
                if ($detail['pattern'] && isset($collectionFiles[$detail['pattern']])) {
                    $result[$key]['pattern'] = array_values($collectionFiles[$detail['pattern']]);
                } else {
                    $result[$key]['pattern'] = null;
                }
                if ($detail['model'] && isset($collectionFiles[$detail['model']])) {
                    $result[$key]['model'] = array_values($collectionFiles[$detail['model']]);
                } else {
                    $result[$key]['model'] = null;
                }
                if ($detail['project'] && isset($collectionFiles[$detail['project']])) {
                    $result[$key]['project'] = array_values($collectionFiles[$detail['project']]);
                } else {
                    $result[$key]['project'] = null;
                }
            }

            return array_values($result);
        }

        return null;
    }
}