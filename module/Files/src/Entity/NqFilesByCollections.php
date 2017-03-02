<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @ORM\Entity
 */
class NqFilesByCollections
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $id;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(name="fileId", type="integer", nullable=false)
     */
    private $fileId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return ResultSetMapping
     */
    static public function getRsm()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Files\Entity\NqFilesByCollections', 'nq');
        $rsm->addFieldResult('nq', 'id',        'id');
        $rsm->addFieldResult('nq', 'fileId',    'fileId');
        $rsm->addFieldResult('nq', 'name',      'name');
        $rsm->addFieldResult('nq', 'path',      'path');
        $rsm->addFieldResult('nq', 'date',      'date');

        return $rsm;
    }

    /**
     * @param int|array $collectionIDs
     * @return null|string
     */
    static public function getSql($collectionIDs)
    {
        if (empty($collectionIDs))
            return null;
        elseif (is_array($collectionIDs))
            $collectionIDs = implode(",", $collectionIDs);

        $sql = "
SELECT * FROM (
  SELECT
    ims_files_collections.id,
    ims_files.fileId,
    ims_files.fileName AS name,
    ims_file_versions.versionPath AS path,
    ims_file_versions.versionDate AS date
    FROM
      ims_files
      JOIN ims_file_versions ON ims_file_versions.versionFileId = ims_files.fileId
      LEFT JOIN ims_files_collections ON ims_files_collections.fileId = ims_files.fileId
      ORDER BY versionDate DESC
      LIMIT 18446744073709551615
  ) as sub
WHERE id IN ($collectionIDs)
GROUP BY fileId;
";

        return $sql;
    }
}
