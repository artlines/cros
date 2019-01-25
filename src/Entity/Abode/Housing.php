<?php

namespace App\Entity\Abode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Housing
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="housing")
 * @ORM\Entity()
 */
class Housing
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_of_floors", type="integer", nullable=false)
     */
    private $numOfFloors;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var ArrayCollection|Apartment[]
     *
     * @ORM\OneToMany(targetEntity="Apartment", mappedBy="housing")
     */
    private $apartments;

    /**
     * Housing constructor.
     */
    public function __construct()
    {
        $this->apartments = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getNumOfFloors()
    {
        return $this->numOfFloors;
    }

    /**
     * @param int $numOfFloors
     */
    public function setNumOfFloors($numOfFloors)
    {
        $this->numOfFloors = $numOfFloors;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return Apartment[]|ArrayCollection
     */
    public function getApartments()
    {
        return $this->apartments;
    }

    /**
     * @param Apartment[]|ArrayCollection $apartments
     */
    public function setApartments($apartments)
    {
        $this->apartments = $apartments;
    }
}