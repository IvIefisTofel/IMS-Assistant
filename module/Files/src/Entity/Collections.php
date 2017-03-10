<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilesCollections
 *
 * @ORM\Table(name="files_collections")
 * @ORM\Entity
 */
class Collections
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(name="fileId", type="integer", nullable=false)
     * @ORM\Id
     */
    private $fileId;


    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get File Id
     * @return int
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * Set File Id
     * @param int $fileId
     */
    public function setFileId(int $fileId)
    {
        $this->fileId = $fileId;
    }
}