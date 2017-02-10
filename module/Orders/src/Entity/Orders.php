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
class Orders extends OrdersEntity
{
    const STATUS_ORDER  = 1;
    const STATUS_WORK   = 2;
    const STATUS_DONE   = 3;

    public static $STATUS = [
        self::STATUS_ORDER  => "Заказ",
        self::STATUS_WORK   => "В работе",
        self::STATUS_DONE   => "Исполнено",
    ];

    /**
     * Set clientId
     *
     * @param integer $clientId
     *
     * @return Orders
     */
    public function setClientId($clientId)
    {
        $this->clientId = $clientId;

        return $this;
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
     * Set Status
     *
     * @param integer $status
     *
     * @return Orders
     * @throws \Exception
     */
    public function setStatus($status)
    {
        if (isset(self::$STATUS[$status])) {
            $this->status = $status;
            return $this;
        } else {
            throw new \Exception("Not valid status id.");
        }
    }

    /**
     * Set Date Creation
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDateCreation($date)
    {
        if ($date instanceof \DateTime) {
            $this->dateCreation = $date;
        } else {
            $this->dateCreation = new \DateTime($date);
        }

        return $this;
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
        if ($date instanceof \DateTime) {
            $this->dateStart = $date;
        } elseif ($date != null) {
            $this->dateStart = new \DateTime($date);
        } else {
            $this->dateStart = null;
        }

        return $this;
    }

    /**
     * Set Date End
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDateEnd($date)
    {
        if ($date instanceof \DateTime) {
            $this->dateEnd = $date;
        } elseif ($date != null) {
            $this->dateEnd = new \DateTime($date);
        } else {
            $this->dateEnd = null;
        }

        return $this;
    }

    /**
     * Set Deadline
     *
     * @param \DateTime $date
     *
     * @return Orders
     */
    public function setDateDeadline($date)
    {
        if ($date instanceof \DateTime) {
            $this->dateDeadline = $date;
        } elseif ($date != null) {
            $this->dateDeadline = new \DateTime($date);
        } else {
            $this->dateDeadline = null;
        }

        return $this;
    }
}