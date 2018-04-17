<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lecture
 *
 * @ORM\Table(name="lecture")
 * @ORM\Entity
 */
class Lecture
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="time", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="time", nullable=false)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="hall", type="string", length=50, nullable=false)
     */
    private $hall;

    /**
     * @var string
     *
     * @ORM\Column(name="speaker", type="string", length=50, nullable=false)
     */
    private $speaker;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=50, nullable=false)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="moderator", type="string", length=50, nullable=false)
     */
    private $moderator;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="theses", type="text", nullable=false)
     */
    private $theses;

    /**
     * @var integer
     *
     * @ORM\Column(name="hall_id", type="integer", nullable=true)
     */
    private $hallId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tgchat", mappedBy="lecture")
     */
    private $tgchat;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tgchat = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

