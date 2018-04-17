<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Apartament
 *
 * @ORM\Table(name="apartament", indexes={@ORM\Index(name="IDX_551D61F995A1E69", columns={"pair"})})
 * @ORM\Entity
 */
class Apartament
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="places", type="integer", nullable=false)
     */
    private $places;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", precision=10, scale=0, nullable=false)
     */
    private $price;

    /**
     * @var integer
     *
     * @ORM\Column(name="conference_id", type="integer", nullable=false)
     */
    private $conferenceId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ApartamentPair
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ApartamentPair")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pair", referencedColumnName="id")
     * })
     */
    private $pair;


}

