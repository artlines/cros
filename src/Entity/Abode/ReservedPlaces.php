<?php

namespace App\Entity\Abode;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ReservedPlaces
 * @package App\Entity\Abode
 *
 * @ORM\Table(
 *     schema="abode",
 *     name="reserved_places",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="unique_reserved_places_idx", columns={"room_type_id", "housing_id"})}
 * )
 * @ORM\Entity()
 */
class ReservedPlaces
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
     * @ORM\ManyToOne(targetEntity="RoomType")
     * @ORM\JoinColumn(name="room_type_id", referencedColumnName="id", nullable=false)
     */
    private $roomType;

    /**
     * @var Housing
     *
     * @ORM\ManyToOne(targetEntity="Housing")
     * @ORM\JoinColumn(name="housing_id", referencedColumnName="id", nullable=false)
     */
    private $housing;

    /**
     * @var integer
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

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
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * @param RoomType $roomType
     */
    public function setRoomType(RoomType $roomType)
    {
        $this->roomType = $roomType;
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
    public function setHousing(Housing $housing)
    {
        $this->housing = $housing;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = $count;
    }
}