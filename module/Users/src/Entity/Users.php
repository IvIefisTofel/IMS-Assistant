<?php

namespace Users\Entity;

use Doctrine\ORM\Mapping as ORM;
use MCms\Entity\MCmsEntity;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class Users extends  MCmsEntity
{
    /**
     * Getting client IP Address
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    */

    const ADMIN_ROLE     = 1;
    const MODERATOR_ROLE = 2;
    const USER_ROLE      = 3;
    const GUEST_ROLE     = 4;

    public static $ROLE_NAME = array(
        1 => 'Admin',
        2 => 'Moderator',
        3 => 'User',
        4 => 'Guest',
    );

    public static $ROLE_LABEL = array(
        1 => 'Администратор',
        2 => 'Модератор',
        3 => 'Пользователь',
        4 => 'Гость',
    );

    /**
     * @var integer
     *
     * @ORM\Column(name="userId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="userFullName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $fullName;

    /**
     * @var string
     *
     * @ORM\Column(name="userEmail", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="userPassword", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    protected $password;

    /**
     * @var integer
     *
     * @ORM\Column(name="userRoleId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $role;

    /**
     * @var boolean
     *
     * @ORM\Column(name="userActive", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="userRegistrationDate", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $registrationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="userEmailConfirmed", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    protected $emailConfirmed = false;

    public function __construct()
    {
        $this->registrationDate = new \DateTime();
    }

    /**
     * Get user Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user Name
     *
     * @param string $name
     * @return Users
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get user Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user FullName
     *
     * @param string $fullName
     * @return Users
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get user FullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Set user Email
     *
     * @param string $email
     * @return Users
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get user Email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user Password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = md5($password);

        return $this;
    }

    /**
     * Get user Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set user Role
     *
     * @param integer $role
     * @return Users
     */
    public function setRoleID($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get user Role
     *
     * @return integer
     */
    public function getRoleID()
    {
        return $this->role;
    }

    /**
     * Get user Role Name
     *
     * @return string
     */
    public function getRoleName()
    {
        return self::$ROLE_NAME[$this->role];
    }

    /**
     * Get user Role Label
     *
     * @return string
     */
    public function getRoleLabel()
    {
        return self::$ROLE_LABEL[$this->role];
    }

    /**
     * Set user Active
     *
     * @param boolean $active
     * @return Users
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get user Active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set user Registration Date
     *
     * @param \DateTime $registrationDate
     * @return Users
     */
    public function setRegistrationDate($registrationDate)
    {
        if ($registrationDate instanceof \DateTime) {
            $this->registrationDate = $registrationDate;
        } elseif ($registrationDate != null) {
            $this->registrationDate = new \DateTime($registrationDate);
        } else {
            $this->registrationDate = null;
        }

        return $this;
    }

    /**
     * Get user Registration Date
     *
     * @return \DateTime
     */
    public function getRegistrationDate($formatted = true)
    {
        if ($formatted)
            return $this->getRegistrationDateFormat();
        else
            return $this->registrationDate;
    }

    /**
     * Get Formatted Registration Date
     *
     * @param string $format
     *
     * @return \DateTime
     */
    public function getRegistrationDateFormat($format = "d.m.Y")
    {
        if ($this->registrationDate != null)
            return date_format($this->registrationDate, $format);
        else
            return null;
    }

    /**
     * Set user Email Confirmed
     *
     * @param boolean $emailConfirmed
     * @return Users
     */
    public function setEmailConfirmed($emailConfirmed)
    {
        $this->emailConfirmed = $emailConfirmed;

        return $this;
    }

    /**
     * Get userEmailConfirmed
     *
     * @return boolean
     */
    public function getEmailConfirmed()
    {
        return $this->emailConfirmed;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boolean $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public function getGrAvatar( $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $this->email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        if ( $img ) {
            $url = '<img src="' . $url . '"';
            foreach ( $atts as $key => $val )
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}