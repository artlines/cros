<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_8D93D649444F97DD", columns={"phone"}), @ORM\UniqueConstraint(name="UNIQ_8D93D64943320DA", columns={"telegram"})}, indexes={@ORM\Index(name="IDX_8D93D64932C8A3DE", columns={"organization_id"}), @ORM\Index(name="IDX_8D93D649479CD6B0", columns={"firstclass"}), @ORM\Index(name="IDX_8D93D649A3EA123", columns={"manager_group_id"})})
 * @ORM\Entity
 */
class User
{
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="string", length=255, nullable=true)
     */
    private $post;

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
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram", type="string", length=255, nullable=true)
     */
    private $telegram;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255, nullable=true)
     */
    private $nickname;

    /**
     * @var string
     *
     * @ORM\Column(name="car_number", type="string", length=255, nullable=true)
     */
    private $carNumber;

    /**
     * @var boolean
     *
     * @ORM\Column(name="saved", type="boolean", nullable=true)
     */
    private $saved;

    /**
     * @var string
     *
     * @ORM\Column(name="change_log", type="text", length=65535, nullable=true)
     */
    private $changeLog;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="regdate", type="datetime", nullable=true)
     */
    private $regdate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="female", type="boolean", nullable=true)
     */
    private $female;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="arrival", type="datetime", nullable=false)
     */
    private $arrival = '2018-05-16 14:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="leaving", type="datetime", nullable=false)
     */
    private $leaving = '2018-05-19 12:00:00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="tm_add", type="datetime", nullable=false)
     */
    private $tmAdd = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;

    /**
     * @var \AppBundle\Entity\ManagerGroup
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ManagerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="manager_group_id", referencedColumnName="id")
     * })
     */
    private $managerGroup;

    /**
     * @var \AppBundle\Entity\Apartament
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Apartament")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="firstclass", referencedColumnName="id")
     * })
     */
    private $firstclass;


}

