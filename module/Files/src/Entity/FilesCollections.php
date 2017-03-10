<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FilesCollections
 *
 * @ORM\Table(name="view_files_collections")
 * @ORM\Entity
 */
class FilesCollections
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
     * @var string
     * @ORM\Column(name="fileName", type="string", length=255, nullable=false)
     */
    private $name;


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

    /**
     * Get File Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}