<?php

namespace App\Entity\Abode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Room
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="room")
 * @ORM\Entity(repositoryClass="App\Repository\Abode\RoomRepository")
 */
class Room
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
     * @var RoomType
     *
     * @ORM\ManyToOne(targetEntity="RoomType", inversedBy="rooms")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @var Apartment
     *
     * @ORM\ManyToOne(targetEntity="Apartment", inversedBy="rooms")
     * @ORM\JoinColumn(name="apartment_id", referencedColumnName="id", nullable=false)
     */
    private $apartment;

    /**
     * @var ArrayCollection|Place[]
     *
     * @ORM\OneToMany(targetEntity="Place", mappedBy="room")
     */
    private $places;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->places = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return RoomType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param RoomType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Apartment
     */
    public function getApartment()
    {
        return $this->apartment;
    }

    /**
     * @param Apartment $apartment
     */
    public function setApartment($apartment)
    {
        $this->apartment = $apartment;
    }

    /**
     * @return Place[]|ArrayCollection
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param Place[]|ArrayCollection $places
     */
    public function setPlaces($places)
    {
        $this->places = $places;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return bool
     */
    public function isEmpty()
    {
        return $this->places->isEmpty();
    }
}