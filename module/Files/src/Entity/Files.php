<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class Files extends MCmsEntity
{
    const UPLOAD_DIR = './upload/';

    /**
     * @var integer
     * @ORM\Column(name="fileId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="parentId", type="integer", nullable=true)
     */
    protected $parentId;

    /**
     * @var string
     * @ORM\Column(name="fileName", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int | null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     * @return Files
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Set name
     * @param string $name
     * @return Files
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}