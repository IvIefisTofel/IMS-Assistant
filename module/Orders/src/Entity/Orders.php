<?php

namespace Orders\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Orders
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity
 */
class Orders extends MCmsEntity
{
    const STATUS_ORDER  = 1;
    const STATUS_WORK   = 2;
    const STATUS_DONE   = 3;

    public static $STATUS = [
        self::STATUS_ORDER  => "Заказ",
        self::STATUS_WORK   => "В работе",
        self::STATUS_DONE   => "Исполненно",
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="orderId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var \Clients\Entity\Clients
     *
     * @ORM\ManyToOne(targetEntity="Clients\Entity\Clients", inversedBy="orders")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="orderClientId", referencedColumnName="clientId", nullable=false)
     * })
     */
    protected $client;

    /**
     * @var string
     *
     * @ORM\Column(name="orderCode", type="string", length=255, nullable=false, unique=true)
     */
    protected $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderStatus", type="integer", nullable=false)
     */
    protected $status;

    /**
     * @var \Nomenclature\Entity\Details
     *
     * @ORM\OneToMany(targetEntity="Nomenclature\Entity\Details", mappedBy="order")
     * @ORM\OrderBy({"dateCreation" = "DESC"})
     */
    protected $details;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderCreationDate", type="date", nullable=false)
     */
    protected $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderStartDate", type="date", nullable=true)
     */
    protected $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderEndDate", type="date", nullable=true)
     */
    protected $dateEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDeadline", type="date", nullable=true)
     */
    protected $dateDeadline;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCreation = new \DateTime();
        $this->details = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client
     *
     * @param \Clients\Entity\Clients $client
     *
     * @return Orders
     */
    public function setClient(\Clients\Entity\Clients $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Clients\Entity\Clients
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set Code
     *
     * @param string $code
     *
     * @return Orders
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get Code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set Status
     *
     * @param integer $status
     *
     * @return Orders
     * @throws \Exception
     */
    public function setStatus($status)
    {
        if (isset(\Orders\Entity\Orders::$STATUS[$status])) {
            $this->status = $status;
            return $this;
        } else {
            throw new \Exception("Not valid status id.");
        }
    }

    /**
     * Get Status
     *
     * @return integer
     */
    public function getStatus()
    {
        return self::$STATUS[$this->status];
    }

    /**
     * Get Status
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->status;
    }

// TODO Реализовать addDetail && removeDetail
//    /**
//     * Add detail
//     *
//     * @param \Nomenclature\Entity\Details $detail
//     *
//     * @return Orders
//     */
//    public function addDetail(\Nomenclature\Entity\Details $detail)
//    {
//        $this->details[] = $detail;
//
//        return $this;
//    }
//
//    /**
//     * Remove detail
//     *
//     * @param \Nomenclature\Entity\Details $detail
//     */
//    public function removeDetail(\Nomenclature\Entity\Details $detail)
//    {
//        $this->details->removeElement($detail);
//    }

    /**
     * Get details
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Get Date Creation
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
     * Get Formatted Date Creation
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateCreationFormat($format = "d.m.Y")
    {
        return date_format($this->dateCreation, $format);
    }

    /**
     * Set Date Start
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDateStart($date)
    {
        $this->dateCreation = $date;

        return $this;
    }

    /**
     * Get Date Start
     *
     * @param bool $formatted
     *
     * @return \DateTime
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
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateStartFormat($format = "d.m.Y")
    {
        if ($this->dateStart != null)
            return date_format($this->dateStart, $format);
        else
            return null;
    }

    /**
     * Set Date End
     *
     * @param \DateTime $dateEnd
     *
     * @return Orders
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;
    }

    /**
     * Get Date End
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
     * Get Formatted Date End
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

    /**
     * Set Deadline
     *
     * @param \DateTime $dateDeadline
     *
     * @return Orders
     */
    public function setDateDeadline($dateDeadline)
    {
        $this->dateDeadline = $dateDeadline;

        return $this;
    }

    /**
     * Get Deadline
     *
     * @param bool $formatted
     *
     * @return \DateTime
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
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getDateDeadlineFormat($format = "d.m.Y")
    {
        if ($this->dateDeadline != null)
            return date_format($this->dateDeadline, $format);
        else
            return null;
    }

    public function toArray()
    {
        $result = parent::toArray();

        $result['clientId'] = $result['client']->getId();
        $result['clientName'] = $result['client']->getName();
        unset($result['client'], $result['details']);

        return $result;
    }
}