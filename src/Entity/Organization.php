<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Organization
 *
 * @ORM\Table(name="organization")
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 */
class Organization implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="bigint")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="requisites", type="text", nullable=true)
     */
    private $requisites;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="manager", type="integer", nullable=true)
     */
    private $manager;

    /**
     * @var string
     *
     * @ORM\Column(name="inn", type="string", length=255)
     */
    private $inn;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     */
    private $kpp;

    /**
     * @var bool
     *
     * @ORM\Column(name="sponsor", type="boolean")
     */
    private $sponsor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=true)
     */
    private $regdate;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(name="our_comment", type="text", nullable=true)
     */
    private $ourComment;

    /**
     * @ORM\Column(name="hidden", type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="organization")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="OrganizationStatus", inversedBy="organizations")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    private $txtstatus;

    /**
     * @ORM\OneToMany(targetEntity="OrgToConf", mappedBy="organization")
     */
    private $otc;

    /**
     * @ORM\ManyToOne(targetEntity="ManagerGroup", inversedBy="managed")
     * @ORM\JoinColumn(name="manager", referencedColumnName="id")
     */
    private $managers;

    /**
     * @var string
     */
    private $entityName = 'organization';

    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Organization
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Organization
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set requisites
     *
     * @param string $requisites
     * @return Organization
     */
    public function setRequisites($requisites)
    {
        $this->requisites = $requisites;

        return $this;
    }

    /**
     * Get requisites
     *
     * @return string 
     */
    public function getRequisites()
    {
        return $this->requisites;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Organization
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Organization
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
     * Add user
     *
     * @param \App\Entity\User $user
     *
     * @return Organization
     */
    public function addUser(\App\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\User $user
     */
    public function removeUser(\App\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set txtstatus
     *
     * @param \App\Entity\OrganizationStatus $txtstatus
     *
     * @return Organization
     */
    public function setTxtstatus(\App\Entity\OrganizationStatus $txtstatus = null)
    {
        $this->txtstatus = $txtstatus;

        return $this;
    }

    /**
     * Get txtstatus
     *
     * @return \App\Entity\OrganizationStatus
     */
    public function getTxtstatus()
    {
        return $this->txtstatus;
    }

    /**
     * Add otc
     *
     * @param \App\Entity\OrgToConf $otc
     *
     * @return Organization
     */
    public function addOtc(\App\Entity\OrgToConf $otc)
    {
        $this->otc[] = $otc;

        return $this;
    }

    /**
     * Remove otc
     *
     * @param \App\Entity\OrgToConf $otc
     */
    public function removeOtc(\App\Entity\OrgToConf $otc)
    {
        $this->otc->removeElement($otc);
    }

    /**
     * Get otc
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOtc()
    {
        return $this->otc;
    }

    /**
     * Set manager
     *
     * @param integer $manager
     *
     * @return Organization
     */
    public function setManager($manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return integer
     */
    public function getManager()
    {
        return $this->manager;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Organization
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set managers
     *
     * @param \App\Entity\ManagerGroup $managers
     *
     * @return Organization
     */
    public function setManagers(\App\Entity\ManagerGroup $managers = null)
    {
        $this->managers = $managers;

        return $this;
    }

    /**
     * Get managers
     *
     * @return \App\Entity\ManagerGroup
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * Set inn
     *
     * @param string $inn
     *
     * @return Organization
     */
    public function setInn($inn)
    {
        $this->inn = $inn;

        return $this;
    }

    /**
     * Get inn
     *
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * Set kpp
     *
     * @param string $kpp
     *
     * @return Organization
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * Get kpp
     *
     * @return string
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * Set sponsor
     *
     * @param boolean $sponsor
     *
     * @return Organization
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * Get sponsor
     *
     * @return boolean
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Organization
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
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set username
     *
     * @param integer $username
     *
     * @return Organization
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
     * Get salt
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return array('ROLE_ORG');
    }

    public function eraseCredentials()
    {

    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }

    /** @see \Serializable::unserialize() */
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
     * Set password
     *
     * @param string $password
     *
     * @return Organization
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
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
     * Set regdate
     *
     * @param \DateTime $regdate
     *
     * @return Organization
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Organization
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ourComment
     *
     * @param string $ourComment
     *
     * @return Organization
     */
    public function setOurComment($ourComment)
    {
        $this->ourComment = $ourComment;

        return $this;
    }

    /**
     * Get ourComment
     *
     * @return string
     */
    public function getOurComment()
    {
        return $this->ourComment;
    }

    /**
     * Set hidden
     *
     * @param boolean $hidden
     *
     * @return Organization
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden
     *
     * @return boolean
     */
    public function getHidden()
    {
        return $this->hidden;
    }
}
