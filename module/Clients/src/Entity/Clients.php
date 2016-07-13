<?php

namespace Clients\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clients
 *
 * @ORM\Table(name="clients")
 * @ORM\Entity
 */
class Clients
{
    /**
     * @var integer
     *
     * @ORM\Column(name="clientId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $clientId;

    /**
     * @var string
     *
     * @ORM\Column(name="clientName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $clientName;

    /**
     * @var string
     *
     * @ORM\Column(name="clientDescription", type="text", length=65535, precision=0, scale=0, nullable=true, unique=false)
     */
    private $clientDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="clientAdditions", type="text", length=65535, precision=0, scale=0, nullable=false, unique=false)
     */
    private $clientAdditions;


    /**
     * Get clientId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->clientId;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     *
     * @return Clients
     */
    public function setName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getName()
    {
        return $this->clientName;
    }

    /**
     * Set clientDescription
     *
     * @param string $clientDescription
     *
     * @return Clients
     */
    public function setDescription($clientDescription)
    {
        $this->clientDescription = $clientDescription;

        return $this;
    }

    /**
     * Get clientDescription
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->clientDescription;
    }

    /**
     * Set clientAdditions
     *
     * @param string $clientAdditions
     *
     * @return Clients
     */
    public function setAdditions($clientAdditions)
    {
        $this->clientAdditions = $clientAdditions;

        return $this;
    }

    /**
     * Get clientAdditions
     *
     * @return string
     */
    public function getAdditions()
    {
        return $this->clientAdditions;
    }
}

