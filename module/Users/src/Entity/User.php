<?php

namespace Users\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="lit_users")
 * @ORM\Entity
 */
class User
{
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
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="userFullName", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $userFullName;

    /**
     * @var string
     *
     * @ORM\Column(name="userEmail", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $userEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="userPassword", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $userPassword;

    /**
     * @var integer
     *
     * @ORM\Column(name="userRoleId", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userRole;

    /**
     * @var boolean
     *
     * @ORM\Column(name="userActive", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="userRegistrationDate", type="datetime", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userRegistrationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="userEmailConfirmed", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     */
    private $userEmailConfirmed = false;

    public function __construct()
    {
        $this->userRegistrationDate = new \DateTime();
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userName
     *
     * @param string $userName
     * @return User
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set userFullName
     *
     * @param string $userFullName
     * @return User
     */
    public function setUserFullName($userFullName)
    {
        $this->userFullName = $userFullName;

        return $this;
    }

    /**
     * Get userFullName
     *
     * @return string
     */
    public function getUserFullName()
    {
        return $this->userFullName;
    }

    /**
     * Set userEmail
     *
     * @param string $userEmail
     * @return User
     */
    public function setUserEmail($userEmail)
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * Get userEmail
     *
     * @return string 
     */
    public function getUserEmail()
    {
        return $this->userEmail;
    }

    /**
     * Set userPassword
     *
     * @param string $userPassword
     * @return User
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = md5($userPassword);

        return $this;
    }

    /**
     * Get userPassword
     *
     * @return string
     */
    public function getUserPassword()
    {
        return $this->userPassword;
    }

    /**
     * Set userRole
     *
     * @param integer $userRole
     * @return User
     */
    public function setUserRoleID($userRole)
    {
        $this->userRole = $userRole;

        return $this;
    }

    /**
     * Get userRole
     *
     * @return integer
     */
    public function getUserRoleID()
    {
        return $this->userRole;
    }

    /**
     * @return string
     */
    public function getUserRoleName()
    {
        return self::$ROLE_NAME[$this->userRole];
    }

    /**
     * @return string
     */
    public function getUserRoleLabel()
    {
        return self::$ROLE_LABEL[$this->userRole];
    }

    /**
     * Set userActive
     *
     * @param boolean $userActive
     * @return User
     */
    public function setUserActive($userActive)
    {
        $this->userActive = $userActive;

        return $this;
    }

    /**
     * Get userActive
     *
     * @return boolean 
     */
    public function getUserActive()
    {
        return $this->userActive;
    }

    /**
     * Set userRegistrationDate
     *
     * @param \DateTime $userRegistrationDate
     * @return User
     */
    public function setUserRegistrationDate($userRegistrationDate)
    {
        $this->userRegistrationDate = $userRegistrationDate;

        return $this;
    }

    /**
     * Get userRegistrationDate
     *
     * @return \DateTime
     */
    public function getUserRegistrationDate()
    {
        return $this->userRegistrationDate;
    }

    /**
     * Set userEmailConfirmed
     *
     * @param boolean $userEmailConfirmed
     * @return User
     */
    public function setUserEmailConfirmed($userEmailConfirmed)
    {
        $this->userEmailConfirmed = $userEmailConfirmed;

        return $this;
    }

    /**
     * Get userEmailConfirmed
     *
     * @return boolean
     */
    public function getUserEmailConfirmed()
    {
        return $this->userEmailConfirmed;
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
        $url .= md5( strtolower( trim( $this->userEmail ) ) );
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