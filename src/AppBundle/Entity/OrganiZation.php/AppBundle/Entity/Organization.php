<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organization
 *
 * @ORM\Table(name="organization", indexes={@ORM\Index(name="IDX_C1EE637CFA2425B9", columns={"manager"}), @ORM\Index(name="IDX_C1EE637C7B00651C", columns={"status"})})
 * @ORM\Entity
 */
class Organization
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=false)
     */
    private $city;

    /**
     * @var integer
     *
     * @ORM\Column(name="phone", type="bigint", nullable=false)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="requisites", type="text", length=65535, nullable=true)
     */
    private $requisites;

    /**
     * @var string
     *
     * @ORM\Column(name="inn", type="string", length=255, nullable=false)
     */
    private $inn;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", length=65535, nullable=true)
     */
    private $address;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sponsor", type="boolean", nullable=false)
     */
    private $sponsor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=true)
     */
    private $regdate;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", length=65535, nullable=true)
     */
    private $comment;

    /**
     * @var string
     *
     * @ORM\Column(name="our_comment", type="text", length=65535, nullable=true)
     */
    private $ourComment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hidden", type="boolean", nullable=true)
     */
    private $hidden;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\ManagerGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ManagerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manager", referencedColumnName="id")
     * })
     */
    private $manager;

    /**
     * @var \AppBundle\Entity\OrganizationStatus
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\OrganizationStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="status", referencedColumnName="id")
     * })
     */
    private $status;


}

