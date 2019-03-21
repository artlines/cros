<?php

namespace App\Entity\Program;

use App\Entity\Participating\ConferenceMember;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProgramMember
 * @package App\Entity\Participating
 *
 * @ORM\Entity()
 * @ORM\Table(schema="program", name="program_member")
 */
class Member
{
    const TYPE_SPEAKER      = 'speaker';
    const TYPE_COMMITTEE    = 'committee';

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var ConferenceMember
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Participating\ConferenceMember")
     * @ORM\JoinColumn(name="conference_member_id", referencedColumnName="id", nullable=false)
     */
    private $conferenceMember;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_original", type="text", nullable=true)
     *
     */
    private $photoOriginal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="photo_big", type="text", nullable=true)
     *
     */
    private $photoBig;

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

    /**
     * @var integer
     *
     * @ORM\Column(name="ordering", type="integer", nullable=false, options={"default":"100"})
     */
    private $ordering;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ConferenceMember
     */
    public function getConferenceMember()
    {
        return $this->conferenceMember;
    }

    /**
     * @param ConferenceMember $conferenceMember
     */
    public function setConferenceMember(ConferenceMember $conferenceMember)
    {
        $this->conferenceMember = $conferenceMember;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getPhotoOriginal(): ?string
    {
        return $this->photoOriginal;
    }

    /**
     * @param null|string $photoOriginal
     */
    public function setPhotoOriginal(?string $photoOriginal)
    {
        $this->photoOriginal = $photoOriginal;
    }

    /**
     * @return null|string
     */
    public function getPhotoBig(): ?string
    {
        return $this->photoBig;
    }

    /**
     * @param null|string $photoBig
     */
    public function setPhotoBig(?string $photoBig)
    {
        $this->photoBig = $photoBig;
    }

    /**
     * @return null|string
     */
    public function getPhotoSmall(): ?string
    {
        return $this->photoSmall;
    }

    /**
     * @param null|string $photoSmall
     */
    public function setPhotoSmall(?string $photoSmall)
    {
        $this->photoSmall = $photoSmall;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * @param int $ordering
     */
    public function setOrdering(int $ordering)
    {
        $this->ordering = $ordering;
    }
}