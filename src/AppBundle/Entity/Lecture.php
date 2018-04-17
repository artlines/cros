<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Lecture
 *
 * @ORM\Table(name="lecture")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LectureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Lecture
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time")
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="hall", type="string", length=50)
     */
    private $hall;

    /**
     * @var int|null
     *
     * @ORM\Column(name="hall_id", type="integer", nullable=true)
     */
    private $hallId;

    /**
     * @var string
     *
     * @ORM\Column(name="speaker", type="string", length=50)
     */
    private $speaker;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=50)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="moderator", type="string", length=50)
     */
    private $moderator;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="theses", type="text")
     */
    private $theses;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="TgChat", mappedBy="lectures")
     */
    private $chats;



    public function __construct()
    {
        $this->chats = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getChats()
    {
        return $this->chats;
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Lecture
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Lecture
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Lecture
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set hall
     *
     * @param string $hall
     *
     * @return Lecture
     */
    public function setHall($hall)
    {
        $this->hall = $hall;

        return $this;
    }

    /**
     * Get hall
     *
     * @return string
     */
    public function getHall()
    {
        return $this->hall;
    }

    /**
     * @return int|null
     */
    public function getHallId()
    {
        return $this->hallId;
    }

    /**
     * @param int|null $hallId
     */
    public function setHallId($hallId)
    {
        $this->hallId = $hallId;
    }

    /**
     * Set speaker
     *
     * @param string $speaker
     *
     * @return Lecture
     */
    public function setSpeaker($speaker)
    {
        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get speaker
     *
     * @return string
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Lecture
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set moderator
     *
     * @param string $moderator
     *
     * @return Lecture
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;

        return $this;
    }

    /**
     * Get moderator
     *
     * @return string
     */
    public function getModerator()
    {
        return $this->moderator;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Lecture
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set theses
     *
     * @param string $theses
     *
     * @return Lecture
     */
    public function setTheses($theses)
    {
        $this->theses = $theses;

        return $this;
    }

    /**
     * Get theses
     *
     * @return string
     */
    public function getTheses()
    {
        return $this->theses;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tgchat;


    /**
     * Add tgchat
     *
     * @param \AppBundle\Entity\Tgchat $tgchat
     *
     * @return Lecture
     */
    public function addTgchat(\AppBundle\Entity\Tgchat $tgchat)
    {
        $this->tgchat[] = $tgchat;

        return $this;
    }

    /**
     * Remove tgchat
     *
     * @param \AppBundle\Entity\Tgchat $tgchat
     */
    public function removeTgchat(\AppBundle\Entity\Tgchat $tgchat)
    {
        $this->tgchat->removeElement($tgchat);
    }

    /**
     * Get tgchat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTgchat()
    {
        return $this->tgchat;
    }
}
