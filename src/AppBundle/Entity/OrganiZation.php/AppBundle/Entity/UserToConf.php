<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserToConf
 *
 * @ORM\Table(name="user_to_conf", indexes={@ORM\Index(name="IDX_AB10866DA76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class UserToConf
{
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
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;


}

