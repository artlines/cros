<?php

namespace App\Entity\Participating;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Organization
 *
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 * @ORM\Table(
 *     schema="participating",
 *     name="organization",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="organization_idx", columns={"kpp", "inn"})}
 * )
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
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
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
     * @var bool
     *
     * @ORM\Column(name="invalid_inn_kpp", type="boolean", nullable=true)
     */
    private $invalidInnKpp;

    /**
     * @ORM\Column(name="hidden", type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="organization")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="text", length=2, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="type_person", type="text", length=1, nullable=true)
     */
    private $typePerson;

    /**
     * Fixed GUID организации из b2b
     * @var string|null
     * @ORM\Column(name="b2b_guid", type="string", nullable=true)
     */
    private $b2b_guid;

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
        $this->isActive     = true;
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
     * @return string|null
     */
    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @param string|null $logo
     */
    public function setLogo(?string $logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return string|null
     */
    public function getNewLogo(): ?string
    {
        return null;
    }

    /**
     * @param mixed
     */
    public function setNewLogo( $logo)
    {
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email)
    {
        $this->email = $email;
    }

    /**
     * Set city
     *
     * @param string|null $city
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
     * @return string|null
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
     * Set country
     *
     * @param string $country
     *
     * @return Organization
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set typePerson
     *
     * @param string $typePerson
     *
     * @return Organization
     */
    public function setTypePerson($typePerson)
    {
        $this->typePerson = $typePerson;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getB2bGuid(): ?string
    {
        return $this->b2b_guid;
    }

    /**
     * @param null|string $b2b_guid
     */
    public function setB2bGuid(?string $b2b_guid)
    {
        $this->b2b_guid = $b2b_guid;
    }

    /**
     * @return bool|null
     */
    public function isInvalidInnKpp(): ?bool
    {
        return $this->invalidInnKpp;
    }

    /**
     * @param bool|null $invalidInnKpp
     */
    public function setInvalidInnKpp(?bool $invalidInnKpp)
    {
        $this->invalidInnKpp = $invalidInnKpp;
    }

    /**
     * Get typePerson
     *
     * @return string
     */
    public function getTypePerson()
    {
        return $this->typePerson;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return "".$this->getName();
    }
}