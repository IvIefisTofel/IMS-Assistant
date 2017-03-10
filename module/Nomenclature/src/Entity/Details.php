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
class Details extends DetailsEntity
{
    /**
     * Set order id
     * @param integer $orderId
     * @return Details
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Set group
     * @param string $group
     * @return Details
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Set code
     * @param string $code
     * @return Details
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set name
     * @param string $name
     * @return Details
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set pattern
     * @param integer $pattern
     * @return Details
     */
    public function setPattern($pattern = null)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Set model
     * @param integer $model
     * @return Details
     */
    public function setModel($model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set project
     * @param integer $project
     * @return Details
     */
    public function setProject($project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Set dateCreation
     * @param \DateTime $date
     * @return Details
     */
    public function setDateCreation($date)
    {
        if ($date instanceof \DateTime) {
            $this->dateCreation = $date;
        } elseif ($date != null) {
            $this->dateCreation = new \DateTime($date);
        }

        return $this;
    }

    /**
     * Set dateEnd
     * @param \DateTime $date
     * @return Details
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
}