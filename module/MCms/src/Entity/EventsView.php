<?php

namespace MCms\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="view_events")
 * @ORM\Entity
 */
class EventsView extends MCmsEntity
{
    /**
     * @var integer
     * @ORM\Column(name="eventId", type="integer", nullable=true, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(name="eventUser", type="integer", nullable=true, unique=false)
     */
    protected $userId;

    /**
     * @var integer
     * @ORM\Column(name="eventType", type="integer", nullable=false, unique=false)
     */
    protected $type;

    /**
     * @var integer
     * @ORM\Column(name="eventEntity", type="integer", nullable=false, unique=false)
     */
    protected $entityId;

    /**
     * @var \DateTime
     * @ORM\Column(name="eventDate", type="datetime", nullable=false, unique=false)
     */
    protected $date;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return int | null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int | null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->entityId;
    }

    /**
     * Get Creation Date
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
    public function getDateFormat($format = "d.m.Y"): string
    {
        if ($this->date != null)
            return date_format($this->date, $format);
        else
            return null;
    }
}