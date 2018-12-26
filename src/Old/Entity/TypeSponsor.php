<?php

namespace App\Old\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SponsorType
 *
 * @ORM\Table(name="sponsor_type")
 * @ORM\Entity()
 */
class TypeSponsor
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
     * @var string
     *
     * @ORM\Column(name="name_type", type="string", length=255)
     */
    private $name_type;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $name_type
     *
     * @return TypeSponsor
     */
    public function setNameType($name_type)
    {
        $this->name_type = $name_type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getNameType()
    {
        return $this->name_type;
    }
}
