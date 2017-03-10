<?php

namespace Nomenclature\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Details
 *
 * @ORM\Table(name="view_details")
 * @ORM\Entity
 */
class DetailsView extends DetailsEntity
{
    /**
     * @var string
     * @ORM\Column(name="orderCode", type="string", length=255, nullable=false)
     */
    protected $orderCode;

    /**
     * Get orderCode
     * @return string
     */
    public function getOrderCode()
    {
        return $this->orderCode;
    }
}