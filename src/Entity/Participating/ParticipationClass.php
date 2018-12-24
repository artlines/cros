<?php

namespace App\Entity\Abode;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ParticipationClass
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="participation_class")
 * @ORM\Entity()
 */
class ParticipationClass
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="RoomType", mappedBy="participationClass")
     */
    private $roomTypes;

    public function __construct()
    {
        $this->roomTypes = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getRoomTypes()
    {
        return $this->roomTypes;
    }

    /**
     * @param ArrayCollection $roomTypes
     */
    public function setRoomTypes($roomTypes)
    {
        $this->roomTypes = $roomTypes;
    }
}