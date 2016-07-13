<?php

namespace Orders\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orders
 *
 * @ORM\Table(name="ims_orders")
 * @ORM\Entity
 */
class Orders
{
    /**
     * @var integer
     *
     * @ORM\Column(name="orderId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $orderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderClientId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $orderClientId;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderGroupId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $orderGroupId;

    /**
     * @var string
     *
     * @ORM\Column(name="orderCode", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $orderCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="orderStatus", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $orderStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="orderNomenclature", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $orderNomenclature;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="orderDeadline", type="date", precision=0, scale=0, nullable=true, unique=false)
     */
    private $orderDeadline;


    /**
     * Get orderId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->orderId;
    }

    /**
     * Set orderClientId
     *
     * @param integer $orderClientId
     *
     * @return Orders
     */
    public function setClientId($orderClientId)
    {
        $this->orderClientId = $orderClientId;

        return $this;
    }

    /**
     * Get orderClientId
     *
     * @return integer
     */
    public function getClientId()
    {
        return $this->orderClientId;
    }

    /**
     * Set orderGroupId
     *
     * @param integer $orderGroupId
     *
     * @return Orders
     */
    public function setGroupId($orderGroupId)
    {
        $this->orderGroupId = $orderGroupId;

        return $this;
    }

    /**
     * Get orderGroupId
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->orderGroupId;
    }

    /**
     * Set orderCode
     *
     * @param string $orderCode
     *
     * @return Orders
     */
    public function setCode($orderCode)
    {
        $this->orderCode = $orderCode;

        return $this;
    }

    /**
     * Get orderCode
     *
     * @return string
     */
    public function getCode()
    {
        return $this->orderCode;
    }

    /**
     * Set orderStatus
     *
     * @param integer $orderStatus
     *
     * @return Orders
     */
    public function setStatus($orderStatus)
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * Get orderStatus
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->orderStatus;
    }

    /**
     * Set orderNomenclature
     *
     * @param string $orderNomenclature
     *
     * @return Orders
     */
    public function setNomenclature($orderNomenclature)
    {
        $this->orderNomenclature = $orderNomenclature;

        return $this;
    }

    /**
     * Get orderNomenclature
     *
     * @return string
     */
    public function getNomenclature()
    {
        return $this->orderNomenclature;
    }

    /**
     * Set orderDeadline
     *
     * @param \DateTime $orderDeadline
     *
     * @return Orders
     */
    public function setDeadline($orderDeadline)
    {
        $this->orderDeadline = $orderDeadline;

        return $this;
    }

    /**
     * Get orderDeadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->orderDeadline;
    }
}

