<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TypeSponsor
 *
 * @ORM\Table(name="type_sponsor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TypeSponsorRepository")
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
    public function setType($name_type)
    {
        $this->name_type = $name_type;

        return $this;
    }
    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->name_type;
    }
}
