<?php

namespace Nomenclature\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Details
 *
 * @ORM\Table(name="details")
 * @ORM\Entity
 */
class Details extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="detailId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailOrderId", type="integer", nullable=false)
     */
    protected $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="detailGroup", type="string", length=255, nullable=true)
     */
    protected $group;

    /**
     * @var string
     *
     * @ORM\Column(name="detailCode", type="string", length=255, nullable=false)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="detailName", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailPattern", type="integer", nullable=true)
     */
    protected $pattern;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailModel", type="integer", nullable=true)
     */
    protected $model;

    /**
     * @var integer
     *
     * @ORM\Column(name="detailProject", type="integer", nullable=true)
     */
    protected $project;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="detailCreationDate", type="date", nullable=false)
     */
    protected $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="detailEndDate", type="date", nullable=true)
     */
    protected $dateEnd = null;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
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
     * Set order id
     *
     * @param integer $orderId
     *
     * @return Details
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get order id
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return Details
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Details
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Details
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
     * Set pattern
     *
     * @param integer $pattern
     *
     * @return Details
     */
    public function setPattern($pattern = null)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return integer
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set model
     *
     * @param integer $model
     *
     * @return Details
     */
    public function setModel($model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return integer
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set project
     *
     * @param integer $project
     *
     * @return Details
     */
    public function setProject($project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return integer
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Get dateCreation
     *
     * @param bool $formatted
     *
     * @return \DateTime
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
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateCreationFormat($format = "d.m.Y")
    {
        if ($this->dateCreation != null)
            return date_format($this->dateCreation, $format);
        else
            return null;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Details
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @param bool $formatted
     *
     * @return \DateTime
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
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateEndFormat($format = "d.m.Y")
    {
        if ($this->dateEnd != null)
            return date_format($this->dateEnd, $format);
        else
            return null;
    }
}