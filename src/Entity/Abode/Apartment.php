<?php

namespace App\Entity\Abode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Apartment
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="apartment")
 * @ORM\Entity(repositoryClass="App\Repository\Abode\ApartmentRepository")
 */
class Apartment
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
     * @ORM\Column(name="`number`", type="integer", nullable=false, unique=true)
     */
    private $number;

    /**
     * @var integer
     *
     * @ORM\Column(name="floor_number", type="integer", nullable=false)
     */
    private $floorNumber;

    /**
     * @var Housing
     *
     * @ORM\ManyToOne(targetEntity="Housing", inversedBy="apartments")
     * @ORM\JoinColumn(name="housing_id", referencedColumnName="id", nullable=false)
     */
    private $housing;

    /**
     * @var ApartmentType
     *
     * @ORM\ManyToOne(targetEntity="ApartmentType", inversedBy="apartments")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @var Room[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Room", mappedBy="apartment", cascade={"remove"})
     */
    private $rooms;

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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getFloorNumber()
    {
        return $this->floorNumber;
    }

    /**
     * @param int $floorNumber
     */
    public function setFloorNumber($floorNumber)
    {
        $this->floorNumber = $floorNumber;
    }

    /**
     * @return Housing
     */
    public function getHousing()
    {
        return $this->housing;
    }

    /**
     * @param Housing $housing
     */
    public function setHousing($housing)
    {
        $this->housing = $housing;
    }

    /**
     * @return ApartmentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param ApartmentType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Room[]|ArrayCollection
     */
    public function getRooms()
    {
        return $this->rooms;
    }

    /**
     * @param Room[]|ArrayCollection $rooms
     */
    public function setRooms($rooms)
    {
        $this->rooms = $rooms;
    }
}