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
     * @var \Orders\Entity\Orders
     *
     * @ORM\ManyToOne(targetEntity="Orders\Entity\Orders", inversedBy="details")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detailOrderId", referencedColumnName="orderId", nullable=false)
     * })
     *
     */
    protected $order;

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
     * @var \Files\Entity\Files
     *
     * @ORM\ManyToOne(targetEntity="Files\Entity\Files", inversedBy="id")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detailPattern", referencedColumnName="fileId")
     * })
     */
    protected $pattern;

    /**
     * @var \Files\Entity\Files
     *
     * @ORM\ManyToOne(targetEntity="Files\Entity\Files", inversedBy="id")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detailModel", referencedColumnName="fileId")
     * })
     */
    protected $model;

    /**
     * @var \Files\Entity\Files
     *
     * @ORM\ManyToOne(targetEntity="Files\Entity\Files", inversedBy="id")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detailProject", referencedColumnName="fileId")
     * })
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
     * Set order
     *
     * @param \Orders\Entity\Orders $order
     *
     * @return Details
     */
    public function setOrder(\Orders\Entity\Orders $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \Orders\Entity\Orders
     */
    public function getOrder()
    {
        return $this->order;
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
     * @param \Files\Entity\Files $pattern
     *
     * @return Details
     */
    public function setPattern(\Files\Entity\Files $pattern = null)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return \Files\Entity\Files
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set model
     *
     * @param \Files\Entity\Files $model
     *
     * @return Details
     */
    public function setModel(\Files\Entity\Files $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get model
     *
     * @return \Files\Entity\Files
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set project
     *
     * @param \Files\Entity\Files $project
     *
     * @return Details
     */
    public function setProject(\Files\Entity\Files $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Files\Entity\Files
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

    public function toArray()
    {
        $result = parent::toArray();

        $result['orderId'] = $result['order']->getId();
        $result['orderCode'] = $result['order']->getCode();
        $result['pattern'] = ($result['pattern'] == null) ? null
            : $result['pattern']->getName() . "." . $result['pattern']->getExt();
        $result['model'] = ($result['model'] == null) ? null
            : $result['model']->getName() . "." . $result['model']->getExt();
        $result['project'] = ($result['project'] == null) ? null
            : $result['project']->getName() . "." . $result['project']->getExt();
        unset($result['order']);

        return $result;
    }
}