<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 * @ORM\Table(name="stage", indexes={@ORM\Index(name="IDX_C27C9369F2382094", columns={"flat1"}), @ORM\Index(name="IDX_C27C93696B31712E", columns={"flat2"}), @ORM\Index(name="IDX_C27C93691C3641B8", columns={"flat3"}), @ORM\Index(name="IDX_C27C93698252D41B", columns={"flat4"})})
 * @ORM\Entity
 */
class Stage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="max_flat", type="integer", nullable=false)
     */
    private $maxFlat;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Flat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flat1", referencedColumnName="id")
     * })
     */
    private $flat1;

    /**
     * @var \AppBundle\Entity\Flat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flat4", referencedColumnName="id")
     * })
     */
    private $flat4;

    /**
     * @var \AppBundle\Entity\Flat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flat2", referencedColumnName="id")
     * })
     */
    private $flat2;

    /**
     * @var \AppBundle\Entity\Flat
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Flat")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="flat3", referencedColumnName="id")
     * })
     */
    private $flat3;


}

