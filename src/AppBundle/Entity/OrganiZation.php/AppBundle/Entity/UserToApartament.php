<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserToApartament
 *
 * @ORM\Table(name="user_to_apartament", indexes={@ORM\Index(name="IDX_9CA02ED4A76ED395", columns={"user_id"}), @ORM\Index(name="IDX_9CA02ED497D8FD11", columns={"apartaments_id"})})
 * @ORM\Entity
 */
class UserToApartament
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="approved", type="boolean", nullable=false)
     */
    private $approved;

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
     *   @ORM\JoinColumn(name="apartaments_id", referencedColumnName="id")
     * })
     */
    private $apartaments;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}

