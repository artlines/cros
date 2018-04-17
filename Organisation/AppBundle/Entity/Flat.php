<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Flat
 *
 * @ORM\Table(name="flat", indexes={@ORM\Index(name="IDX_554AAA44E4733A2A", columns={"room1"}), @ORM\Index(name="IDX_554AAA447D7A6B90", columns={"room2"}), @ORM\Index(name="IDX_554AAA44A7D5B06", columns={"room3"}), @ORM\Index(name="IDX_554AAA449419CEA5", columns={"room4"}), @ORM\Index(name="IDX_554AAA44E31EFE33", columns={"room5"}), @ORM\Index(name="IDX_554AAA44C54C8C93", columns={"type_id"})})
 * @ORM\Entity
 */
class Flat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="maxroom", type="integer", nullable=false)
     */
    private $maxroom;

    /**
     * @var integer
     *
     * @ORM\Column(name="finished", type="integer", nullable=false)
     */
    private $finished;

    /**
     * @var integer
     *
     * @ORM\Column(name="real_id", type="integer", nullable=true)
     */
    private $realId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ApartamentId
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentId")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room5", referencedColumnName="id")
     * })
     */
    private $room5;

    /**
     * @var \AppBundle\Entity\ApartamentId
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentId")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room1", referencedColumnName="id")
     * })
     */
    private $room1;

    /**
     * @var \AppBundle\Entity\ApartamentType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     * })
     */
    private $type;

    /**
     * @var \AppBundle\Entity\ApartamentId
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentId")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room3", referencedColumnName="id")
     * })
     */
    private $room3;

    /**
     * @var \AppBundle\Entity\ApartamentId
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentId")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room4", referencedColumnName="id")
     * })
     */
    private $room4;

    /**
     * @var \AppBundle\Entity\ApartamentId
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentId")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room2", referencedColumnName="id")
     * })
     */
    private $room2;


}

