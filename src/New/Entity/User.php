<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(schema="", name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="string", length=255, nullable=true)
     */
    private $post;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="bigint", unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=false)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram", type="string", length=255, nullable=true, unique=true)
     */
    private $telegram;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=false, unique=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255, nullable=true, unique=false)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="car_number", type="string", length=255, nullable=true, unique=false)
     */
    private $carNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="saved", type="boolean", nullable=true)
     */
    private $saved;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=true)
     */
    private $regdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="female", type="boolean", nullable=true)
     */
    private $female;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival", type="datetime", options={"default": "2018-05-16 14:00"})
     */
    private $arrival;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="departures", type="datetime", options={"default": "2018-05-19 14:00"})
     */
    private $departures;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="leaving", type="datetime", options={"default": "2018-05-19 12:00"})
     */
    private $leaving;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="users")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    private $organization;

    /**
     * @ORM\OneToMany(targetEntity="Speaker", mappedBy="user")
     */
    private $speakers;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tm_add", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $tmAdd;

    private $entityName = 'user';

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Conference", inversedBy="users")
     * @ORM\JoinTable(name="users_conferences",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")}
     * )
     */
    private $conferences;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->utocs = new ArrayCollection();
        $this->speakers = new ArrayCollection();
        $this->arrival = new \DateTime("2018-05-16 14:00");
        $this->departures = new \DateTime("2018-05-19 14:00");
        $this->leaving = new \DateTime("2018-05-19 12:00");
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set post
     *
     * @param string $post
     * @return User
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return string
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set username
     *
     * @param integer $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = str_replace('+', '', $username);

        return $this;
    }

    /**
     * Get username
     *
     * @return integer
     */
    public function getUsername()
    {
        return $this->username != null ? '+'.$this->username : null;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set telegram
     *
     * @param string $telegram
     * @return User
     */
    public function setTelegram($telegram)
    {
        $this->telegram = $telegram;

        return $this;
    }

    /**
     * Get telegram
     *
     * @return string
     */
    public function getTelegram()
    {
        return $this->telegram;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get salt
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles){
        $this->roles = json_encode($roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return json_decode($this->roles, 1);
    }

    public function eraseCredentials()
    {

    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            ) = unserialize($serialized);
    }

    /**
     * Set organization
     *
     * @param \App\Entity\Organization $organization
     *
     * @return User
     */
    public function setOrganization(\App\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \App\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Add utoc
     *
     * @param \App\Entity\UserToConf $utoc
     *
     * @return User
     */
    public function addUtoc(\App\Entity\UserToConf $utoc)
    {
        $this->utocs[] = $utoc;

        return $this;
    }

    /**
     * Remove utoc
     *
     * @param \App\Entity\UserToConf $utoc
     */
    public function removeUtoc(\App\Entity\UserToConf $utoc)
    {
        $this->utocs->removeElement($utoc);
    }

    /**
     * Get utocs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUtocs()
    {
        return $this->utocs;
    }

    /**
     * Set carNumber
     *
     * @param string $carNumber
     *
     * @return User
     */
    public function setCarNumber($carNumber)
    {
        $this->carNumber = $carNumber;

        return $this;
    }

    /**
     * Get carNumber
     *
     * @return string
     */
    public function getCarNumber()
    {
        return $this->carNumber;
    }

    /**
     * Get entityName
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set saved
     *
     * @param bool $saved
     *
     * @return User
     */
    public function setSaved($saved)
    {
        $this->saved = $saved;

        return $this;
    }

    /**
     * Get saved
     *
     * @return boolean
     */
    public function getSaved()
    {
        return $this->saved;
    }

    /**
     * Set regdate
     *
     * @param \DateTime $regdate
     *
     * @return User
     */
    public function setRegdate($regdate)
    {
        $this->regdate = $regdate;

        return $this;
    }

    /**
     * Get regdate
     *
     * @return \DateTime
     */
    public function getRegdate()
    {
        return $this->regdate;
    }

    /**
     * Set female
     *
     * @param integer $female
     *
     * @return User
     */
    public function setFemale($female)
    {
        $this->female = $female;

        return $this;
    }

    /**
     * Get female
     *
     * @return integer
     */
    public function getFemale()
    {
        return $this->female;
    }

    /**
     * Add speaker
     *
     * @param \App\Entity\Speaker $speaker
     *
     * @return User
     */
    public function addSpeaker(\App\Entity\Speaker $speaker)
    {
        $this->speakers[] = $speaker;

        return $this;
    }

    /**
     * Remove speaker
     *
     * @param \App\Entity\Speaker $speaker
     */
    public function removeSpeaker(\App\Entity\Speaker $speaker)
    {
        $this->speakers->removeElement($speaker);
    }

    /**
     * Get speakers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSpeakers()
    {
        return $this->speakers;
    }

    /**
     * Set arrival
     *
     * @param \DateTime $arrival
     *
     * @return User
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;

        return $this;
    }
    /**
     * Set departures
     *
     * @param \DateTime $departures
     *
     * @return User
     */
    public function setDepartures($departures)
    {
        $this->departures = $departures;

        return $this;
    }
    /**
     * Get arrival
     *
     * @return \DateTime
     */
    public function getArrival()
    {
        return $this->arrival;
    }
    /**
     * Get departures
     *
     * @return \DateTime
     */
    public function getDepartures()
    {
        return $this->departures;
    }
    /**
     * @return \DateTime
     */
    public function getLeaving()
    {
        return $this->leaving;
    }

    /**
     * @param \DateTime $leaving
     *
     * @return User
     */
    public function setLeaving($leaving)
    {
        $this->leaving = $leaving;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTmAdd()
    {
        return $this->tmAdd;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setTmAdd()
    {
        $this->tmAdd = new \DateTime();
    }
}
