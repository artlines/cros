<?php

namespace App\Entity\Participating;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Organization
 *
 * @ORM\Table(schema="participating", name="organization")
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 *
 * TODO: ADD LOGO FIELD
 */
class Organization
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

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
     * @ORM\Column(name="is_active", type="boolean", nullable=false, options={"default": 1})
     */
    private $isActive;

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
     * @ORM\Column(name="hidden", type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="organization")
     */
    private $users;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var string
     */
    private $entityName = 'organization';

    /**
     * Organization constructor.
     */
    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->users        = new ArrayCollection();
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @param \App\Entity\Participating\User $user
     *
     * @return Organization
     */
    public function addUser(\App\Entity\Participating\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\Participating\User $user
     */
    public function removeUser(\App\Entity\Participating\User $user)
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
     * Get entityName
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
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
