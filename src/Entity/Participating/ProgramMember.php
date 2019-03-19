<?php

namespace App\Entity\Participating;

/**
 * Class ProgramMember
 * @package App\Entity\Participating
 *
 * @ORM\Table(schema="program", name="program_member")
 */
class ProgramMember
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
     * @var string|null
     *
     * @ORM\Column(name="photo", type="text", nullable=true)
     *
     */
    private $photo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_big", type="text", nullable=true)
     *
     */
    private $photoB;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_small", type="text", nullable=true)
     *
     */
    private $photoSmall;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

}