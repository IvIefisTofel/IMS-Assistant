<?php

namespace MCms\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @ORM\Table(name="view_events")
 * @ORM\Entity
 */
class EventsView extends MCmsEntity
{
    /**
     * @var string
     * @ORM\Column(name="eventId", type="string", nullable=true, unique=false)
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
     * @return string
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

    /**
     * @return ResultSetMapping
     */
    static public function getRsm()
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(self::class, 'nq');
        $rsm->addFieldResult('nq', 'eventId',     'id');
        $rsm->addFieldResult('nq', 'eventUser',   'userId');
        $rsm->addFieldResult('nq', 'eventType',   'type');
        $rsm->addFieldResult('nq', 'eventEntity', 'entityId');
        $rsm->addFieldResult('nq', 'eventDate',   'date');

        return $rsm;
    }

    /**
     * @param array $opts
     * @param string $prefix
     * @return null|string
     */
    static public function getSql($opts = [], $prefix)
    {
        /**
         * Array user Names
         */
        $users = isset($opts['users']) ? $opts['users'] : null;
        if (is_array($users)) {
            $users = '"' . implode('","', $users) . '"';
        } elseif ($users != null) {
            $users = '"' . $users . '"';
        }
        $users = ($users != null) ? "eventUser IN (SELECT userId FROM ims_users WHERE userName IN ($users))" : '';

        /**
         * Array eventId
         */
        $arrIds = isset($opts['ids']) ? $opts['ids'] : null;
        if (is_array($arrIds)) {
            $arrIds = '"' . implode('","', $arrIds) . '"';
        } elseif ($arrIds != null) {
            $arrIds = '"' . $arrIds . '"';
        }
        $arrIds = ($arrIds != null) ? "eventId IN ($arrIds)" : '';

        /**
         * Date
         */
        $dateFrom = isset($opts['date']['from']) ? $opts['date']['from'] : null;
        if (is_string($dateFrom)) {
            $dateFrom = date_format(new \DateTime($dateFrom), 'Y-m-d');
        } elseif (is_a($dateFrom, 'DateTime')) {
            $dateFrom = $dateFrom->format('Y-m-d');
        }
        $dateTo = isset($opts['date']['to']) ? $opts['date']['to'] : null;
        if (is_string($dateTo)) {
            $dateTo = date_format(new \DateTime($dateTo), 'Y-m-d');
        } elseif (is_a($dateTo, 'DateTime')) {
            $dateTo = $dateTo->format('Y-m-d');
        }
        $date = ($dateFrom != null || $dateTo != null)
            ? '(' .
                (($dateFrom != null) ? "eventDate >= '$dateFrom'" : '') .
                (($dateTo != null) ? (($dateFrom != null) ? " AND " : '') . "eventDate <= '$dateTo'" : '') .
            ')'
            : '';

        /**
         * WHERE
        */
        $where = '';
        if ($date . $arrIds . $users != '') {
            $where .= $users;

            if ($where != '' && $arrIds != '') { $where .= " AND "; }
            $where .= $arrIds;

            if ($where != '' && $date != '') { $where .= " AND "; }
            $where .= $date;
        }
        if ($where != '') {
            $where = " WHERE $where";
        }

        /**
         * ORDER BY
         */
        $orderBy = isset($opts['orderBy']) ? $opts['orderBy'] : '';
        if (is_array($orderBy)) {
            foreach ($orderBy as $key => $item) {
                $orderBy[$key] = "$key $item";
            }
            $orderBy = implode(",", $orderBy);
        }
        if ($orderBy != '') {
            if (substr($orderBy, 0, 8) == "ORDER BY") {
                $orderBy = " $orderBy";
            } else {
                $orderBy = " ORDER BY $orderBy";
            }
        }

        /**
         * LIMIT
         */
        $limit = $offset = '';
        if (isset($opts['limit'])) {
            $limit = " LIMIT " . $opts['limit'];
            /**
             * OFFSET
             */
            $offset = isset($opts['offset']) ? " OFFSET " . $opts['offset'] : '';
        };
        
        $sql = "SELECT * FROM " . $prefix . "view_events" . $where . $orderBy . $limit . $offset;

        return $sql;
    }
}