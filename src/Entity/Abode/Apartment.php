<?php

namespace App\Entity\Abode;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Apartment
 * @package App\Entity\Abode
 *
 * @ORM\Table(schema="abode", name="apartment")
 * @ORM\Entity()
 */
class Apartment
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="`number`", type="integer", nullable=false, unique=true)
     */
    private $number;

    /**
     * @var integer
     *
     * @ORM\Column(name="floor_number", type="integer", nullable=false)
     */
    private $floorNumber;

    /**
     * @var Housing
     *
     * @ORM\ManyToOne(targetEntity="Housing", inversedBy="apartments")
     * @ORM\JoinColumn(name="housing_id", referencedColumnName="id", nullable=false)
     */
    private $housing;

    /**
     * @var ApartmentType
     *
     * @ORM\ManyToOne(targetEntity="ApartmentType", inversedBy="apartments")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id", nullable=false)
     */
    private $type;
}