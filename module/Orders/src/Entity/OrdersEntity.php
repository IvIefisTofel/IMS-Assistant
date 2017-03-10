<?php

namespace Orders\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

class OrdersEntity extends MCmsEntity
{
    /**
     * @var integer
     * @ORM\Column(name="orderId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="orderClientId", type="integer", nullable=false)
     */
    protected $clientId;

    /**
     * @var string
     * @ORM\Column(name="orderCode", type="string", length=255, nullable=false, unique=true)
     */
    protected $code;

    /**
     * @var integer
     * @ORM\Column(name="orderStatus", type="integer", nullable=false)
     */
    protected $status;

    /**
     * @var \DateTime
     * @ORM\Column(name="orderCreationDate", type="date", nullable=false)
     */
    protected $dateCreation;

    /**
     * @var \DateTime
     * @ORM\Column(name="orderStartDate", type="date", nullable=true)
     */
    protected $dateStart;

    /**
     * @var \DateTime
     * @ORM\Column(name="orderEndDate", type="date", nullable=true)
     */
    protected $dateEnd;

    /**
     * @var \DateTime
     * @ORM\Column(name="orderDeadline", type="date", nullable=true)
     */
    protected $dateDeadline;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    /**
     * Get Id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get clientId
     * @return integer
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get Code
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get Status
     * @return string
     */
    public function getStatus()
    {
        return Orders::$STATUS[$this->status];
    }

    /**
     * Get Status
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Get Date Creation
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
     * Get Formatted Date Creation
     * @param string $format
     * @return string
     */
    public function getDateCreationFormat($format = "d.m.Y")
    {
        return date_format($this->dateCreation, $format);
    }

    /**
     * Get Date Start
     * @param bool $formatted
     * @return string | \DateTime
     */
    public function getDateStart($formatted = true)
    {
        if ($formatted)
            return $this->getDateStartFormat();
        else
            return $this->dateStart;
    }

    /**
     * Get Formatted Date Start
     * @param string $format
     * @return string
     */
    public function getDateStartFormat($format = "d.m.Y")
    {
        if ($this->dateStart != null)
            return date_format($this->dateStart, $format);
        else
            return null;
    }

    /**
     * Get Date End
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
     * Get Formatted Date End
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

    /**
     * Get Deadline
     * @param bool $formatted
     * @return string | \DateTime
     */
    public function getDateDeadline($formatted = true)
    {
        if ($formatted)
            return $this->getDateDeadlineFormat();
        else
            return $this->dateDeadline;
    }

    /**
     * Get Formatted Deadline
     * @param string $format
     * @return string
     */
    public function getDateDeadlineFormat($format = "d.m.Y")
    {
        if ($this->dateDeadline != null)
            return date_format($this->dateDeadline, $format);
        else
            return null;
    }
}