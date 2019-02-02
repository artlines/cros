<?php

namespace App\Entity\Abode;

use App\Entity\Participating\ParticipationClass;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RoomType
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="room_type")
 * @ORM\Entity()
 */
class RoomType
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
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_places", type="integer", nullable=false)
     */
    private $maxPlaces;

    /**
     * @var integer
     *
     * @ORM\Column(name="cost", type="integer", nullable=false)
     */
    private $cost;

    /**
     * @var ParticipationClass
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Participating\ParticipationClass", inversedBy="roomTypes")
     * @ORM\JoinColumn(name="participation_class_id", referencedColumnName="id", nullable=true)
     */
    private $participationClass;

    /**
     * @var Room[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RoomType", mappedBy="type")
     */
    private $rooms;

    /**
     * @ORM\Column(type="text")
     */
    private $Description;

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
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getMaxPlaces()
    {
        return $this->maxPlaces;
    }

    /**
     * @param int $maxPlaces
     */
    public function setMaxPlaces($maxPlaces)
    {
        $this->maxPlaces = $maxPlaces;
    }

    /**
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param int $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    /**
     * @return ParticipationClass
     */
    public function getParticipationClass()
    {
        return $this->participationClass;
    }

    /**
     * @param ParticipationClass $participationClass
     */
    public function setParticipationClass($participationClass)
    {
        $this->participationClass = $participationClass;
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
    public function __toString()
    {
        return $this->getTitle(). ' Мест: '. $this->getMaxPlaces() .' Цена: '. $this->getCost();
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }
}