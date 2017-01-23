<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * FileVersion
 *
 * @ORM\Table(name="file_versions")
 * @ORM\Entity
 */
class FileVersion extends MCmsEntity
{
    const VERSIONS_MAX_COUNT = 5;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="versionFileId", type="integer", nullable=false)
     */
    protected $fileId;

    /**
     * @var string
     *
     * @ORM\Column(name="versionPath", type="string", length=255, nullable=false)
     */
    protected $path;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="versionDate", type="date", nullable=false)
     */
    protected $date;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get file id
     *
     * @return int
     */
    public function getFileId(): int
    {
        return $this->fileId;
    }

    /**
     * Get file id
     *
     * @param int $fileId
     */
    public function setFileId(int $fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return FileVersion
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get date
     *
     * @param bool $formatted
     *
     * @return \DateTime
     */
    public function getDate($formatted = true)
    {
        if ($formatted)
            return $this->getDateFormat();
        else
            return $this->date;
    }

    /**
     * Get Formatted End Date
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateFormat($format = "d.m.Y")
    {
        if ($this->date != null)
            return date_format($this->date, $format);
        else
            return null;
    }
}