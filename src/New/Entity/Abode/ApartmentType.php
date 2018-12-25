<?php

namespace App\Entity\Abode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ApartmentType
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="apartment_type")
 * @ORM\Entity()
 */
class ApartmentType
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_rooms", type="integer", nullable=false)
     */
    private $maxRooms;

    /**
     * @var ArrayCollection|Apartment[]
     *
     * @ORM\OneToMany(targetEntity="Apartment", mappedBy="type")
     */
    private $apartments;

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
     * @return int
     */
    public function getMaxRooms()
    {
        return $this->maxRooms;
    }

    /**
     * @param int $maxRooms
     */
    public function setMaxRooms($maxRooms)
    {
        $this->maxRooms = $maxRooms;
    }
}