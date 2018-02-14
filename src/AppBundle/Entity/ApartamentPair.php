<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentPair
 *
 * @ORM\Table(name="apartament_pair")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ApartamentPairRepository")
 */
class ApartamentPair
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
     * @ORM\Column(name="pair", type="string", length=255, unique=true)
     */
    private $pair;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=false)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="Apartament", mappedBy="apartamentPair")
     */
    private $apartaments;

    /**
     * @ORM\OneToMany(targetEntity="ApartamentType", mappedBy="pairs")
     */
    private $apartamentTypes;

    /**
     * ApartamentPair constructor.
     */
    public function __construct()
    {
        $this->apartaments = new ArrayCollection();
    }


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
     * Set pair
     *
     * @param string $pair
     *
     * @return ApartamentPair
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
     * Add apartament
     *
     * @param \AppBundle\Entity\Apartament $apartament
     *
     * @return ApartamentPair
     */
    public function addApartament(\AppBundle\Entity\Apartament $apartament)
    {
        $this->apartaments[] = $apartament;

        return $this;
    }

    /**
     * Remove apartament
     *
     * @param \AppBundle\Entity\Apartament $apartament
     */
    public function removeApartament(\AppBundle\Entity\Apartament $apartament)
    {
        $this->apartaments->removeElement($apartament);
    }

    /**
     * Get apartaments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApartaments()
    {
        return $this->apartaments;
    }

    /**
     * Add apartamentType
     *
     * @param \AppBundle\Entity\ApartamentType $apartamentType
     *
     * @return ApartamentPair
     */
    public function addApartamentType(\AppBundle\Entity\ApartamentType $apartamentType)
    {
        $this->apartamentTypes[] = $apartamentType;

        return $this;
    }

    /**
     * Remove apartamentType
     *
     * @param \AppBundle\Entity\ApartamentType $apartamentType
     */
    public function removeApartamentType(\AppBundle\Entity\ApartamentType $apartamentType)
    {
        $this->apartamentTypes->removeElement($apartamentType);
    }

    /**
     * Get apartamentTypes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApartamentTypes()
    {
        return $this->apartamentTypes;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ApartamentPair
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
}
