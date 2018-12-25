<?php

namespace App\Old\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentType
 *
 * @ORM\Table(name="apartament_type")
 * @ORM\Entity()
 */
class ApartamentType
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
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var int
     *
     * @ORM\Column(name="apartament_pair", type="integer")
     */
    private $apartamentPair;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentPair", inversedBy="apartamentTypes")
     * @ORM\JoinColumn(name="apartament_pair", referencedColumnName="id")
     */
    private $pairs;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="type")
     * @ORM\OrderBy({"realId" = "ASC"})
     */
    private $flats;


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
     * @return ApartamentType
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
     * Constructor
     */
    public function __construct()
    {
        $this->apartaments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->flats = new ArrayCollection();
    }

    /**
     * Set apartamentPair
     *
     * @param integer $apartamentPair
     *
     * @return ApartamentType
     */
    public function setApartamentPair($apartamentPair)
    {
        $this->apartamentPair = $apartamentPair;

        return $this;
    }

    /**
     * Get apartamentPair
     *
     * @return integer
     */
    public function getApartamentPair()
    {
        return $this->apartamentPair;
    }

    /**
     * Set pairs
     *
     * @param \App\Old\Entity\ApartamentPair $pairs
     *
     * @return ApartamentType
     */
    public function setPairs(\App\Old\Entity\ApartamentPair $pairs = null)
    {
        $this->pairs = $pairs;

        return $this;
    }

    /**
     * Get pairs
     *
     * @return \App\Old\Entity\ApartamentPair
     */
    public function getPairs()
    {
        return $this->pairs;
    }

    /**
     * Add flat
     *
     * @param \App\Old\Entity\Flat $flat
     *
     * @return ApartamentType
     */
    public function addFlat(\App\Old\Entity\Flat $flat)
    {
        $this->flats[] = $flat;

        return $this;
    }

    /**
     * Remove flat
     *
     * @param \App\Old\Entity\Flat $flat
     */
    public function removeFlat(\App\Old\Entity\Flat $flat)
    {
        $this->flats->removeElement($flat);
    }

    /**
     * Get flats
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats()
    {
        return $this->flats;
    }
}
