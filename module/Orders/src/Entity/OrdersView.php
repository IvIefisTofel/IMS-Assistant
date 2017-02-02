<?php

namespace Orders\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Orders
 *
 * @ORM\Table(name="view_orders")
 * @ORM\Entity
 */
class OrdersView extends OrdersEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="clientName", type="string", length=255, nullable=false, unique=true)
     */
    protected $clientName;

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }
}