<?php

namespace Nomenclature\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

class DetailsEntity extends MCmsEntity
{
    /**
     * @var integer
     * @ORM\Column(name="detailId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="detailOrderId", type="integer", nullable=true)
     */
    protected $orderId;

    /**
     * @var string
     * @ORM\Column(name="detailGroup", type="string", length=255, nullable=true)
     */
    protected $group;

    /**
     * @var string
     * @ORM\Column(name="detailCode", type="string", length=255, nullable=false)
     */
    protected $code;

    /**
     * @var string
     * @ORM\Column(name="detailName", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     * @ORM\Column(name="detailPattern", type="integer", nullable=true)
     */
    protected $pattern;

    /**
     * @var integer
     * @ORM\Column(name="detailModel", type="integer", nullable=true)
     */
    protected $model;

    /**
     * @var integer
     * @ORM\Column(name="detailProject", type="integer", nullable=true)
     */
    protected $project;

    /**
     * @var \DateTime
     * @ORM\Column(name="detailCreationDate", type="date", nullable=false)
     */
    protected $dateCreation;

    /**
     * @var \DateTime
     * @ORM\Column(name="detailEndDate", type="date", nullable=true)
     */
    protected $dateEnd = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }


    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get order id
     * @return integer | null
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Get group
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get pattern
     * @return integer
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Get model
     * @return integer
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get project
     * @return integer
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Get dateCreation
     * @param bool $formatted
     * @return string | \DateTime
     */
    public function getDateCreation($formatted = true)
    {
        if ($formatted)
            return $this->getDateCreationFormat();
        else
            return $this->dateCreation;
    }

    /**
     * Get Formatted End Date
     * @param string $format
     * @return string
     */
    public function getDateCreationFormat($format = "d.m.Y")
    {
        if ($this->dateCreation != null)
            return date_format($this->dateCreation, $format);
        else
            return null;
    }

    /**
     * Get dateEnd
     * @param bool $formatted
     * @return string | \DateTime
     */
    public function getDateEnd($formatted = true)
    {
        if ($formatted)
            return $this->getDateEndFormat();
        else
            return $this->dateEnd;
    }

    /**
     * Get Formatted End Date
     * @param string $format
     * @return string
     */
    public function getDateEndFormat($format = "d.m.Y")
    {
        if ($this->dateEnd != null)
            return date_format($this->dateEnd, $format);
        else
            return null;
    }
}