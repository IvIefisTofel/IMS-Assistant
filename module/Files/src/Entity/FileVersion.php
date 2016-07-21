<?php

namespace Files\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * FileVersion
 *
 * @ORM\Table(name="fileversion")
 * @ORM\Entity
 */
class FileVersion extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="versionId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Files\Entity\Files
     * @ORM\ManyToOne(targetEntity="Files\Entity\Files", inversedBy="versions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="versionFileId", referencedColumnName="fileId", nullable=false)
     * })
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="versionPath", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $path;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="versionDate", type="date", precision=0, scale=0, nullable=false, unique=false)
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
     * Set file
     *
     * @param \Files\Entity\Files $file
     *
     * @return FileVersion
     */
    public function setFile(\Files\Entity\Files $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \Files\Entity\Files
     */
    public function getFile()
    {
        return $this->file;
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

    public function toArray()
    {
        $result = parent::toArray();

        $result['fileId'] = $result['file']->getId();
        $result['fileName'] = $result['file']->getName() . "." . $result['file']->getExt();
        unset($result['file']);

        return $result;
    }
}