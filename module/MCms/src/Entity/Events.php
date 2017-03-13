<?php

namespace MCms\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="events")
 * @ORM\Entity
 */
class Events extends EventsView
{
    const E_DEADLINE_MISSED = 1;
    const E_CLIENT_CREATE   = 2;
    const E_CLIENT_UPDATE   = 3;
    const E_ORDER_CREATE    = 4;
    const E_ORDER_UPDATE    = 5;
    const E_ORDER_START     = 6;
    const E_ORDER_END       = 7;
    const E_DETAIL_CREATE   = 8;
    const E_DETAIL_UPDATE   = 9;
    const E_DETAIL_END      = 10;
    const E_PATTERN_CREATE  = 11;
    const E_PATTERN_UPDATE  = 12;
    const E_MODEL_CREATE    = 13;
    const E_MODEL_UPDATE    = 14;
    const E_PROJECT_CREATE  = 15;
    const E_PROJECT_UPDATE  = 16;

    public static function getEntityClass($eventType) {
        if (in_array($eventType, [
            self::E_CLIENT_CREATE,
            self::E_CLIENT_UPDATE,
        ])) {
            return \Clients\Entity\Clients::class;
        }
        if (in_array($eventType, [
            self::E_DEADLINE_MISSED,
            self::E_ORDER_CREATE,
            self::E_ORDER_UPDATE,
            self::E_ORDER_START,
            self::E_ORDER_END,
        ])) {
            return \Orders\Entity\Orders::class;
        }
        if (in_array($eventType, [
            self::E_DETAIL_CREATE,
            self::E_DETAIL_UPDATE,
            self::E_DETAIL_END,
            self::E_PATTERN_CREATE,
            self::E_PATTERN_UPDATE,
            self::E_MODEL_CREATE,
            self::E_MODEL_UPDATE,
            self::E_PROJECT_CREATE,
            self::E_PROJECT_UPDATE,
        ])) {
            return \Nomenclature\Entity\Details::class;
        }

        return false;
    }

    const E_TEXTS = [
        self::E_CLIENT_CREATE   => 'Пользователь {user} создал клиента {client}.',
        self::E_CLIENT_UPDATE   => 'Пользователь {user} обновил информацию о клиенте {client}.',
        self::E_ORDER_CREATE    => 'Пользователь {user} создал заказ {order}.',
        self::E_ORDER_UPDATE    => 'Пользователь {user} обновил информацию о заказе {order}.',
        self::E_ORDER_START     => 'Пользователь {user} принял заказ {order} в работу.',
        self::E_ORDER_END       => 'Пользователь {user} закрыл заказ {order}.',
        self::E_DEADLINE_MISSED => 'У заказа {order} истек крайний срок.',
        self::E_DETAIL_CREATE   => 'Пользователь {user} создал деталь {detail}.',
        self::E_DETAIL_UPDATE   => 'Пользователь {user} обновил информацию о детали {detail}.',
        self::E_DETAIL_END      => 'Пользователь {user} закрыл деталь {detail}.',
        self::E_PATTERN_CREATE  => 'Пользователь {user} добавил чертеж в деталь {detail}.',
        self::E_PATTERN_UPDATE  => 'Пользователь {user} обновил чертеж в детали {detail}.',
        self::E_MODEL_CREATE    => 'Пользователь {user} добавил модель в деталь {detail}.',
        self::E_MODEL_UPDATE    => 'Пользователь {user} обновил модель в детали {detail}.',
        self::E_PROJECT_CREATE  => 'Пользователь {user} добавил проект в деталь {detail}.',
        self::E_PROJECT_UPDATE  => 'Пользователь {user} обновил проект в детали {detail}.',
    ];

    const E_CLASS_PRIMARY   = 'primary';
    const E_CLASS_INFO      = 'info';
    const E_CLASS_SUCCESS   = 'success';
    const E_CLASS_WARNING   = 'warning';
    const E_CLASS_DANGER    = 'danger';

    const E_CLASSES = [
        self::E_CLIENT_CREATE   => [ 'icon' => 'fa-user-plus',              'class' => null ],
        self::E_CLIENT_UPDATE   => [ 'icon' => 'fa-pencil',                 'class' => self::E_CLASS_INFO ],
        self::E_ORDER_CREATE    => [ 'icon' => 'fa-plus',                   'class' => null ],
        self::E_ORDER_UPDATE    => [ 'icon' => 'fa-pencil',                 'class' => self::E_CLASS_INFO ],
        self::E_ORDER_START     => [ 'icon' => 'fa-play',                   'class' => self::E_CLASS_PRIMARY ],
        self::E_ORDER_END       => [ 'icon' => 'fa-check',                  'class' => self::E_CLASS_SUCCESS ],
        self::E_DEADLINE_MISSED => [ 'icon' => 'fa-exclamation-triangle',   'class' => self::E_CLASS_DANGER ],
        self::E_DETAIL_CREATE   => [ 'icon' => 'fa-plus',                   'class' => null ],
        self::E_DETAIL_UPDATE   => [ 'icon' => 'fa-pencil',                 'class' => self::E_CLASS_INFO ],
        self::E_DETAIL_END      => [ 'icon' => 'fa-check',                  'class' => self::E_CLASS_SUCCESS ],
        self::E_PATTERN_CREATE  => [ 'icon' => 'fa-picture-o',              'class' => self::E_CLASS_WARNING ],
        self::E_PATTERN_UPDATE  => [ 'icon' => 'fa-picture-o',              'class' => self::E_CLASS_INFO ],
        self::E_MODEL_CREATE    => [ 'icon' => 'fa-cubes',                  'class' => self::E_CLASS_WARNING ],
        self::E_MODEL_UPDATE    => [ 'icon' => 'fa-cubes',                  'class' => self::E_CLASS_INFO ],
        self::E_PROJECT_CREATE  => [ 'icon' => 'fa-cogs',                   'class' => self::E_CLASS_WARNING ],
        self::E_PROJECT_UPDATE  => [ 'icon' => 'fa-cogs',                   'class' => self::E_CLASS_INFO ],
    ];

    /**
     * @var integer
     * @ORM\Column(name="eventUser", type="integer", nullable=false, unique=false)
     */
    protected $userId;

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
     * @param int $userId
     */
    public function setUserId(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Set Creation Date
     * @param string | \DateTime $date
     * @return Events
     */
    public function setDate($date)
    {
        if ($date instanceof \DateTime) {
            $this->date = $date;
        } else {
            $this->date = new \DateTime($date);
        }

        return $this;
    }
}