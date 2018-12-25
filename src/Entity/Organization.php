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
     * @ORM\ManyToMany(targetEntity="App\Entity\Conference", inversedBy="organizations")
     * @ORM\JoinTable(name="organizations_conferences",
     *     joinColumns={@ORM\JoinColumn(name="organization_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="conference_id", referencedColumnName="id")}
     * )
     */
    private $conferences;

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
