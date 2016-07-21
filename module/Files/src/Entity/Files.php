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
    /**
     * @var integer
     *
     * @ORM\Column(name="fileId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Files\Entity\FileVersion
     *
     * @ORM\OneToMany(targetEntity="Files\Entity\FileVersion", mappedBy="file")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    protected $versions;

    /**
     * @var string
     *
     * @ORM\Column(name="fileName", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="fileExtension", type="string", length=255, nullable=false)
     */
    protected $ext;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->versions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Files
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set extension
     *
     * @param string $ext
     *
     * @return Files
     */
    public function setExt($ext)
    {
        $this->ext = $ext;

        return $this;
    }

    /**
     * Get ext
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Get versions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    public function toArray()
    {
        $result = parent::toArray();
        $result['lastVersion'] = $result['versions']->last()->toArray();
        $result['versions'] = $result['versions']->toArray();
        $versions = [];
        foreach ($result['versions'] as $version) {
            $versions[] = $version->toArray();
        }
        $result['versions'] = $versions;

        return $result;
    }
}