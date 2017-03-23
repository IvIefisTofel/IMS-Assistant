<?php

namespace Nomenclature\Controller\Plugin;

use MCms\Controller\Plugin\MCmsPlugin;
use MCms\Entity\Events as Event;
use Nomenclature\Entity\Details;
use Nomenclature\Entity\DetailsArchive;
use Nomenclature\Entity\DetailsView;

class DetailsPlugin extends MCmsPlugin
{
    public function toArray($details = null, $options = [])
    {
        if ($details !== null) {
            $onlyNames = isset($options['onlyNames']) ? $options['onlyNames'] : false;
            $allVersions = isset($options['allVersions']) ? $options['allVersions'] : false;
            $saveIds = isset($options['saveIds']) ? $options['saveIds'] : false;

            $result = [];
            if (!is_array($details)) {
                $details = [$details];
            }

            $collections = [];
            foreach ($details as $key => $detail) {
                if (in_array(get_class($detail), [Details::class, DetailsView::class, DetailsArchive::class])) {
                    /* @var $detail Details */
                    if ($onlyNames) {
                        $result[$detail->getId()] = [
                            'id' => $detail->getId(),
                            'orderId' => $detail->getOrderId(),
                            'name' => $detail->getName(),
                            'code' => $detail->getCode(),
                        ];
                    } else {
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
                    }
                } else {
                    throw new \Exception('Array elements must be ' . Details::class . ' or ' . DetailsView::class . ' or ' . DetailsArchive::class . ' class.');
                }
            }

            if (!$onlyNames) {
                $collectionFiles = $this->controller->plugin('files')->getFiles(array_keys($collections), $allVersions);

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
            }

            if ($saveIds) {
                return $result;
            } else {
                return array_values($result);
            }
        }

        return null;
    }

    public function drop($details = null)
    {
        if ($details !== null) {
            reset($details);
            if (key($details) === 0) {
                $detailIds = [];
                foreach ($details as $key => $value) {
                    $detailIds[$value] = $key;
                }
            } else {
                $detailIds = $details;
            }

            $details = $this->entityManager->getRepository('Nomenclature\Entity\Details')->findById(array_keys($detailIds));
            $canBeDeleted = [];
            foreach ($details as $detail) {
                /* @var $detail \Nomenclature\Entity\Details */
                if ($detail->getModel() == null && $detail->getProject() == null) {
                    $canBeDeleted[$detail->getId()] = $detail;
                } else {
                    $collections = [];
                    if ($detail->getModel() != null) {
                        $collections[] = $detail->getModel();
                    }
                    if ($detail->getProject() != null) {
                        $collections[] = $detail->getProject();
                    }

                    $collections = implode(',', $collections);
                    $prefix = $this->getController()->getServiceLocator()->get('Config')['doctrine']['table_prefix'];
                    $sql = "
SELECT f.fileId as id, c.id as collection, f.parentId as parent, count(versionId) as versions
FROM 
  " . $prefix . "file_versions v,
  " . $prefix . "files f,
  " . $prefix . "files_collections c
WHERE
  versionFileId IN (SELECT fileId FROM " . $prefix . "files_collections WHERE id IN ($collections)) AND
  f.fileId = v.versionFileId AND
  f.fileId = c.fileId
GROUP BY f.fileId
";

                    $stmt = $this->entityManager->getConnection()->prepare($sql);
                    $stmt->execute();

                    $delete = true;
                    foreach ($stmt->fetchAll() as $item) {
                        if (
                            $item['parent'] == null ||
                            ($item['parent'] != null && (int)$item['versions'] > 1)
                        ) {
                            $delete = false;
                            break;
                        }
                    };
                    if ($delete) {
                        $canBeDeleted[$detail->getId()] = $detail;
                    } else {
                        $detail->setOrderId(null);
                        $this->entityManager->persist($detail);

                        $event = new Event();
                        $event->setUserId($this->getController()->identity()->getId());
                        $event->setType(Event::E_DETAIL_ARCHIVED);
                        $event->setEntityId($detail->getId());
                        $this->entityManager->persist($event);
                    }
                }
            }

            $deleteCollections = [];
            foreach ($canBeDeleted as $detail) {
                if ($detail->getPattern() != null) {
                    $deleteCollections[] = $detail->getPattern();
                }
                if ($detail->getModel() != null) {
                    $deleteCollections[] = $detail->getModel();
                }
                if ($detail->getProject() != null) {
                    $deleteCollections[] = $detail->getProject();
                }
                $this->entityManager->remove($detail);
            }
            $deleteCollections = $this->entityManager->getRepository(\Files\Entity\Collections::class)->findById($deleteCollections);
            $deleteFiles = [];
            foreach ($deleteCollections as $collectionFile) {
                /* @var $collectionFile \Files\Entity\Collections */
                $deleteFiles[] = $collectionFile->getFileId();
                $this->entityManager->remove($collectionFile);
            }
            $deleteVersions = $this->entityManager->getRepository(\Files\Entity\FileVersion::class)->findByFileId($deleteFiles);
            $deleteFiles = $this->entityManager->getRepository(\Files\Entity\Files::class)->findById($deleteFiles);

            foreach ($deleteFiles as $file) {
                $this->entityManager->remove($file);
            }
            $this->controller->plugin('files')->dropVersions($deleteVersions);
        }
    }
}