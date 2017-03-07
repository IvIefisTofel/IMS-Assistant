<?php

namespace MCms\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="errors")
 * @ORM\Entity
 */
class Errors extends MCmsEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="errId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="errHash", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $hash;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column(name="readed", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $readed = false;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
        $this->hash = md5($message);
    }

    /**
     * @return string
     */
    public function getReaded(): string
    {
        return $this->readed;
    }

    /**
     * @param string $readed
     */
    public function setReaded(string $readed)
    {
        $this->readed = $readed;
    }
}