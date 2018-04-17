<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Speaker
 *
 * @ORM\Table(name="speaker", indexes={@ORM\Index(name="IDX_7B85DB61A76ED395", columns={"user_id"})})
 * @ORM\Entity
 */
class Speaker
{
    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="text", length=65535, nullable=true)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="text", length=65535, nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publish", type="boolean", nullable=true)
     */
    private $publish;

    /**
     * @var integer
     *
     * @ORM\Column(name="conference_id", type="integer", nullable=false)
     */
    private $conferenceId;

    /**
     * @var string
     *
     * @ORM\Column(name="report", type="string", length=255, nullable=true)
     */
    private $report;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_big", type="text", length=65535, nullable=true)
     */
    private $avatarBig;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_small", type="text", length=65535, nullable=true)
     */
    private $avatarSmall;

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

