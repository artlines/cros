<?php

namespace App\Entity;

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
     * @ORM\OneToMany(targetEntity="InfoToConf", mappedBy="conference")
     */
    private $infotoconfs;

    /**
     * @ORM\OneToMany(targetEntity="OrgToConf", mappedBy="conference")
     */
    private $otc;

    public function __construct()
    {
        $this->infotoconfs = new ArrayCollection();
    }


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
     * @return Conferences
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
     * @return Conferences
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
     * @return Conferences
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
     * @return Conferences
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
     * @return Conferences
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
     * Add infotoconf
     *
     * @param \App\Entity\InfoToConf $infotoconf
     *
     * @return Conference
     */
    public function addInfotoconf(\App\Entity\InfoToConf $infotoconf)
    {
        $this->infotoconfs[] = $infotoconf;

        return $this;
    }

    /**
     * Remove infotoconf
     *
     * @param \App\Entity\InfoToConf $infotoconf
     */
    public function removeInfotoconf(\App\Entity\InfoToConf $infotoconf)
    {
        $this->infotoconfs->removeElement($infotoconf);
    }

    /**
     * Get infotoconfs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInfotoconfs()
    {
        return $this->infotoconfs;
    }

    /**
     * Add otc
     *
     * @param \App\Entity\OrgToConf $otc
     *
     * @return Conference
     */
    public function addOtc(\App\Entity\OrgToConf $otc)
    {
        $this->otc[] = $otc;

        return $this;
    }

    /**
     * Remove otc
     *
     * @param \App\Entity\OrgToConf $otc
     */
    public function removeOtc(\App\Entity\OrgToConf $otc)
    {
        $this->otc->removeElement($otc);
    }

    /**
     * Get otc
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOtc()
    {
        return $this->otc;
    }
}
