<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Files
 *
 * @ORM\Table(name="files")
 * @ORM\Entity
 */
class Files
{
    /**
     * @var integer
     *
     * @ORM\Column(name="fileId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $fileId;

    /**
     * @var string
     *
     * @ORM\Column(name="fileName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="fileExtension", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $fileExtension;


    /**
     * Get fileId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->fileId;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Files
     */
    public function setName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * Set fileExtension
     *
     * @param string $fileExtension
     *
     * @return Files
     */
    public function setExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;

        return $this;
    }

    /**
     * Get fileExtension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->fileExtension;
    }
}

