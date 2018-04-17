<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentId
 *
 * @ORM\Table(name="apartament_id", indexes={@ORM\Index(name="IDX_C3B4BB05C3B4BB05", columns={"apartament_id"})})
 * @ORM\Entity
 */
class ApartamentId
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Apartament
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Apartament")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="apartament_id", referencedColumnName="id")
     * })
     */
    private $apartament;


}

