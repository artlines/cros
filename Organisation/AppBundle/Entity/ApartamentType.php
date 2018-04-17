<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentType
 *
 * @ORM\Table(name="apartament_type", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_C4771C4A2B36786B", columns={"title"})}, indexes={@ORM\Index(name="IDX_C4771C4A41F3550A", columns={"apartament_pair"})})
 * @ORM\Entity
 */
class ApartamentType
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

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
     *   @ORM\JoinColumn(name="apartament_pair", referencedColumnName="id")
     * })
     */
    private $apartamentPair;


}

