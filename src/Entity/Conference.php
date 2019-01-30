<?php

namespace App\Entity;

use App\Entity\Content\Info;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Conference
 *
 * @ORM\Table(name="conference")
 * @ORM\Entity(repositoryClass="App\Repository\ConferenceRepository")
 */
class Conference
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="year", type="integer", unique=true)
     */
    private $year;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_start", type="datetime")
     */
    private $registrationStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_finish", type="datetime")
     */
    private $registrationFinish;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_start", type="datetime")
     */
    private $eventStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="event_finish", type="datetime")
     */
    private $eventFinish;

    /**
     * @var ArrayCollection|Info[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Content\Info", mappedBy="conference")
     */
    private $info;

    /**
     * @var int
     *
     * @ORM\Column(name="limit_users_global", type="integer", nullable=true)
     */
    private $limitUsersGlobal;

    /**
     * @var int
     *
     * @ORM\Column(name="limit_users_by_org", type="integer", nullable=true)
     */
    private $limitUsersByOrg;

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
     * Set year
     *
     * @param integer $year
     *
     * @return Conference
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set registrationStart
     *
     * @param \DateTime $registrationStart
     *
     * @return Conference
     */
    public function setRegistrationStart($registrationStart)
    {
        $this->registrationStart = $registrationStart;

        return $this;
    }

    /**
     * Get registrationStart
     *
     * @return \DateTime
     */
    public function getRegistrationStart()
    {
        return $this->registrationStart;
    }

    /**
     * Set registrationFinish
     *
     * @param \DateTime $registrationFinish
     *
     * @return Conference
     */
    public function setRegistrationFinish($registrationFinish)
    {
        $this->registrationFinish = $registrationFinish;

        return $this;
    }

    /**
     * Get registrationFinish
     *
     * @return \DateTime
     */
    public function getRegistrationFinish()
    {
        return $this->registrationFinish;
    }

    /**
     * Set start
     *
     * @param \DateTime $eventStart
     *
     * @return Conference
     */
    public function setEventStart($eventStart)
    {
        $this->eventStart = $eventStart;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getEventStart()
    {
        return $this->eventStart;
    }

    /**
     * Set finish
     *
     * @param \DateTime $eventFinish
     *
     * @return Conference
     */
    public function setEventFinish($eventFinish)
    {
        $this->eventFinish = $eventFinish;

        return $this;
    }

    /**
     * Get finish
     *
     * @return \DateTime
     */
    public function getEventFinish()
    {
        return $this->eventFinish;
    }

    /**
     * @return Info[]|ArrayCollection
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Set limitUsersGlobal
     *
     * @param integer $limitUsersGlobal
     *
     * @return Conference
     */
    public function setLimitUsersGlobal($limitUsersGlobal)
    {
        $this->limitUsersGlobal = $limitUsersGlobal;

        return $this;
    }

    /**
     * Get limitUsersGlobal
     *
     * @return int
     */
    public function getLimitUsersGlobal()
    {
        return $this->limitUsersGlobal;
    }

    /**
     * Set limitUsersByOrg
     *
     * @param integer $limitUsersByOrg
     *
     * @return Conference
     */
    public function setLimitUsersByOrg($limitUsersByOrg)
    {
        $this->limitUsersByOrg = $limitUsersByOrg;

        return $this;
    }

    /**
     * Get limitUsersGlobal
     *
     * @return int
     */
    public function getLimitUsersByOrg()
    {
        return $this->limitUsersByOrg;
    }

    public function __toString()
    {
        return 'Conference::'.$this->getId();
    }

}
