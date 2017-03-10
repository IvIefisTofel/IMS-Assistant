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
     * @ORM\Column(name="errId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="errHash", type="string", length=50, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $hash;

    /**
     * @var string
     * @ORM\Column(name="errTitle", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="errMessage", type="text", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="errDateCreation", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $date;

    /**
     * @var bool
     * @ORM\Column(name="errRead", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $read = false;

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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param integer $limit
     * @return string
     */
    public function getShortMessage($limit = 150): string
    {
        $result = str_replace("  ", " ", str_replace("\n", " ", html_entity_decode(strip_tags($this->message))));
        if (strlen($result) > $limit) {
            if (($pos = strpos($result, "Развертывание стека")) !== false && $pos < $limit) {
                $result = substr($result, 0, $pos - 2) . "...";
            } else {
                $result = substr($result, 0, strpos($result, " ", $limit)) . "...";
            }
        }

        return $result;
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
     * Get user Creation Date
     * @param bool $formatted
     * @return string | \DateTime
     */
    public function getDate($formatted = true)
    {
        if ($formatted)
            return $this->getDateFormat();
        else
            return $this->date;
    }

    /**
     * Get Formatted Creation Date
     * @param string $format
     * @return string
     */
    public function getDateFormat($format = "d.m.Y")
    {
        if ($this->date != null)
            return date_format($this->date, $format);
        else
            return null;
    }

    /**
     * @return bool
     */
    public function getRead(): bool
    {
        return $this->read;
    }

    /**
     * @param bool $read
     */
    public function setRead(bool $read)
    {
        $this->read = $read;
    }
}