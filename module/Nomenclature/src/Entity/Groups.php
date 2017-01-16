<?php

namespace Nomenclature\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Details
 *
 * @ORM\Table(name="view_groups")
 * @ORM\Entity
 */
class Groups extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="group", type="string", length=255, nullable=true)
     * @ORM\Id
     */
    protected $group;

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }
}