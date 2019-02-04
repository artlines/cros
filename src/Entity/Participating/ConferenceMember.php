<?php

namespace App\Entity\Participating;

use App\Entity\Abode\Place;
use App\Entity\Abode\RoomType;
use App\Entity\Conference;
use App\Entity\Participating\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ConferenceMember
 * @package App\Entity\Participating
 *
 * @ORM\Table(schema="participating", name="conference_member")
 * @ORM\Entity(repositoryClass="App\Repository\ConferenceMemberRepository")
 */
class ConferenceMember
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Participating\User",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Conference
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Conference",cascade={"persist"})
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id", nullable=false)
     */
    private $conference;

    /**
     * @var string
     *
     * @ORM\Column(name="car_number", type="string", nullable=true)
     */
    private $carNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival", type="datetime", nullable=true)
     */
    private $arrival;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="leaving", type="datetime", nullable=true)
     */
    private $leaving;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var ConferenceOrganization
     *
     * @ORM\ManyToOne(targetEntity="ConferenceOrganization", inversedBy="conferenceMembers")
     * @ORM\JoinColumn(name="conference_organization_id", referencedColumnName="id", nullable=false)
     */
    private $conferenceOrganization;

    /**
     * @var Place|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Abode\Place", mappedBy="conferenceMember", cascade={"remove"})
     */
    private $place;

    /**
     * @var RoomType|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Abode\RoomType")
     * @ORM\JoinColumn(name="room_type_id", referencedColumnName="id", nullable=true)
     */
    private $roomType;

    /**
     * @var mixed
     *
     * @ORM\ManyToOne(targetEntity="ConferenceMember",cascade={"persist"})
     * @ORM\JoinColumn(name="neighbourhood_id", referencedColumnName="id", nullable=true)
     */
    private $neighbourhood;

    /**
     * ConferenceMember constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Conference
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * @param Conference $conference
     */
    public function setConference($conference)
    {
        $this->conference = $conference;
    }

    /**
     * @return string
     */
    public function getCarNumber()
    {
        return $this->carNumber;
    }

    /**
     * @param string $carNumber
     */
    public function setCarNumber($carNumber)
    {
        $this->carNumber = $carNumber;
    }

    /**
     * @return \DateTime
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @param \DateTime $arrival
     */
    public function setArrival($arrival)
    {
        $this->arrival = $arrival;
    }

    /**
     * @return \DateTime
     */
    public function getLeaving()
    {
        return $this->leaving;
    }

    /**
     * @param \DateTime $leaving
     */
    public function setLeaving($leaving)
    {
        $this->leaving = $leaving;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return ConferenceOrganization
     */
    public function getConferenceOrganization()
    {
        return $this->conferenceOrganization;
    }

    /**
     * @param ConferenceOrganization $conferenceOrganization
     */
    public function setConferenceOrganization(ConferenceOrganization $conferenceOrganization)
    {
        $this->conferenceOrganization = $conferenceOrganization;
    }

    /**
     * @return Place|null
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param Place|null $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * @return RoomType|null
     */
    public function getRoomType()
    {
        return $this->roomType;
    }

    /**
     * @param RoomType|null $roomType
     */
    public function setRoomType($roomType)
    {
        $this->roomType = $roomType;
    }

    /**
     * @return ConferenceMember|null
     */
    public function getNeighbourhood()
    {
        return $this->neighbourhood;
    }

    /**
     * @param mixed $neighbourhood
     */
    public function setNeighbourhood( $neighbourhood)
    {
        $this->neighbourhood = $neighbourhood;
    }
}