<?php

namespace Clients\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Clients
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity
 */
class Clients extends MCmsEntity
{
    /**
     * @var integer
     * @ORM\Column(name="clientId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="clientName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(name="clientDescription", type="text", length=65535, precision=0, scale=0, nullable=true, unique=false)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(name="clientFilesGroup", type="integer", nullable=true)
     */
    protected $filesGroup;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get client Id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client Name
     * @param string $name
     * @return Clients
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get client Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set client Description
     * @param string $description
     * @return Clients
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get client Description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set client Files Group
     * @param integer $filesGroup
     * @return Clients
     */
    public function setFilesGroup($filesGroup)
    {
        $this->filesGroup = $filesGroup;

        return $this;
    }

    /**
     * Get client Files Group
     * @return integer
     */
    public function getAdditions()
    {
        return $this->filesGroup;
    }
}