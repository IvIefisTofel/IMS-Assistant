<?php

namespace Clients\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * Clients
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity
 */
class Clients extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="clientId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="clientName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $name;

    /**
     * @var \Orders\Entity\Orders
     *
     * @ORM\OneToMany(targetEntity="Orders\Entity\Orders", mappedBy="client")
     * @ORM\OrderBy({"dateCreation" = "DESC"})
     */
    protected $orders;

    /**
     * @var string
     *
     * @ORM\Column(name="clientDescription", type="text", length=65535, precision=0, scale=0, nullable=true, unique=false)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="clientAdditions", type="text", length=65535, precision=0, scale=0, nullable=true, unique=false)
     */
    protected $additions;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get client Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client Name
     *
     * @param string $name
     *
     * @return Clients
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get client Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

// TODO Реализовать addOrder && removeOrder
//    /**
//     * Add order
//     *
//     * @param \Orders\Entity\Orders $order
//     *
//     * @return Clients
//     */
//    public function addOrder(\Orders\Entity\Orders $order)
//    {
//        $this->orders[] = $order;
//
//        return $this;
//    }
//
//    /**
//     * Remove order
//     *
//     * @param \Orders\Entity\Orders $order
//     */
//    public function removeOrder(\Orders\Entity\Orders $order)
//    {
//        $this->orders->removeElement($order);
//    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set client Description
     *
     * @param string $description
     *
     * @return Clients
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get client Description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set client Additions
     *
     * @param array $additions
     *
     * @return Clients
     */
    public function setAdditions($additions)
    {
        $this->additions = implode("\n", $additions);

        return $this;
    }

    /**
     * Get client Additions
     *
     * @return string
     */
    public function getAdditions()
    {
        return ($this->additions) ? explode("\n", $this->additions) : null;
    }

    public function toArray()
    {
        $result = parent::toArray();
        unset($result['orders']);

        return $result;
    }
}