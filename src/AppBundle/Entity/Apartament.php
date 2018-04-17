<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Apartament
 *
 * @ORM\Table(name="apartament")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApartamentRepository")
 */
class Apartament
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(name="places", type="integer")
     */
    private $places;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;

    /**
     * @var string
     *
     * @ORM\Column(name="pair", type="integer")
     */
    private $pair;

    /**
     * @ORM\OneToMany(targetEntity="ApartamentId", mappedBy="apartament")
     */
    private $aitoas;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="apartament")
     */
    private $users;

    /**
     * Apartament constructor.
     */

    public function __construct()
    {
        $this->aitoas = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentPair", inversedBy="apartaments")
     * @ORM\JoinColumn(name="pair", referencedColumnName="id")
     */
    private $apartamentPair;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Apartament
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Apartament
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Apartament
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set places
     *
     * @param integer $places
     *
     * @return Apartament
     */
    public function setPlaces($places)
    {
        $this->places = $places;

        return $this;
    }

    /**
     * Get places
     *
     * @return int
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Apartament
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return Apartament
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return int
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Add aitoa
     *
     * @param \AppBundle\Entity\ApartamentId $aitoa
     *
     * @return Apartament
     */
    public function addAitoa(\AppBundle\Entity\ApartamentId $aitoa)
    {
        $this->aitoas[] = $aitoa;

        return $this;
    }

    /**
     * Remove aitoa
     *
     * @param \AppBundle\Entity\ApartamentId $aitoa
     */
    public function removeAitoa(\AppBundle\Entity\ApartamentId $aitoa)
    {
        $this->aitoas->removeElement($aitoa);
    }

    /**
     * Get aitoas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAitoas()
    {
        return $this->aitoas;
    }

    /**
     * Set pair
     *
     * @param string $pair
     *
     * @return Apartament
     */
    public function setPair($pair)
    {
        $this->pair = $pair;

        return $this;
    }

    /**
     * Get pair
     *
     * @return string
     */
    public function getPair()
    {
        return $this->pair;
    }

    /**
     * Set apartamentpair
     *
     * @param \AppBundle\Entity\ApartamentPair $apartamentpair
     *
     * @return Apartament
     */
    public function setApartamentpair(\AppBundle\Entity\ApartamentPair $apartamentpair = null)
    {
        $this->apartamentpair = $apartamentpair;

        return $this;
    }

    /**
     * Get apartamentpair
     *
     * @return \AppBundle\Entity\ApartamentPair
     */
    public function getApartamentpair()
    {
        return $this->apartamentpair;
    }

    /**
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Apartament
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
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
}