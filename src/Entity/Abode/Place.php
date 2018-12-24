<?php

namespace App\Entity\Abode;

use App\Entity\Participating\ConferenceMember;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Place
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="place")
 * @ORM\Entity()
 */
class Place
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
     * @var ConferenceMember
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Participating\ConferenceMember")
     * @ORM\JoinColumn(name="conference_member_id", referencedColumnName="id", nullable=true)
     */
    private $conferenceMember;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Abode\Room")
     * @ORM\JoinColumn(name="room_id", referencedColumnName="id", nullable=false)
     */
    private $room;

    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=false, options={"default": 0})
     */
    private $approved;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ConferenceMember
     */
    public function getConferenceMember()
    {
        return $this->conferenceMember;
    }

    /**
     * @param ConferenceMember $conferenceMember
     */
    public function setConferenceMember($conferenceMember)
    {
        $this->conferenceMember = $conferenceMember;
    }

    /**
     * @return Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }
}