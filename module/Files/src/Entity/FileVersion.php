<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FileVersion
 *
 * @ORM\Table(name="fileversion")
 * @ORM\Entity
 */
class FileVersion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="versionId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $versionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionFileId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $versionFileId;

    /**
     * @var string
     *
     * @ORM\Column(name="versionPath", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $versionPath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="versionDate", type="date", precision=0, scale=0, nullable=false, unique=false)
     */
    private $versionDate;


    /**
     * Get versionId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->versionId;
    }

    /**
     * Set versionFileId
     *
     * @param integer $versionFileId
     *
     * @return FileVersion
     */
    public function setFileId($versionFileId)
    {
        $this->versionFileId = $versionFileId;

        return $this;
    }

    /**
     * Get versionFileId
     *
     * @return integer
     */
    public function getFileId()
    {
        return $this->versionFileId;
    }

    /**
     * Set versionPath
     *
     * @param string $versionPath
     *
     * @return FileVersion
     */
    public function setPath($versionPath)
    {
        $this->versionPath = $versionPath;

        return $this;
    }

    /**
     * Get versionPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->versionPath;
    }

    /**
     * Set versionDate
     *
     * @param \DateTime $versionDate
     *
     * @return FileVersion
     */
    public function setDate($versionDate)
    {
        $this->versionDate = $versionDate;

        return $this;
    }

    /**
     * Get versionDate
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->versionDate;
    }
}

