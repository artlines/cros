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
     * @ORM\Column(name="year", type="integer")
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
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finish", type="datetime")
     */
    private $finish;

    /**
     * @var ArrayCollection|Info[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Content\Info", mappedBy="conference")
     */
    private $info;

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
     * @param \DateTime $start
     *
     * @return Conference
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set finish
     *
     * @param \DateTime $finish
     *
     * @return Conference
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;

        return $this;
    }

    /**
     * Get finish
     *
     * @return \DateTime
     */
    public function getFinish()
    {
        return $this->finish;
    }

    /**
     * @return Info[]|ArrayCollection
     */
    public function getInfo()
    {
        return $this->info;
    }
}
