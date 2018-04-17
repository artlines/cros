<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentPair
 *
 * @ORM\Table(name="apartament_pair", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_41F3550A95A1E69", columns={"pair"})})
 * @ORM\Entity
 */
class ApartamentPair
{
    /**
     * @var string
     *
     * @ORM\Column(name="pair", type="string", length=255, nullable=false)
     */
    private $pair;

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


}

