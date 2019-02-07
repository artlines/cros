<?php

namespace App\Entity\Participating;

use App\Entity\Conference;
use Doctrine\ORM\Mapping as ORM;

/**
 * Speaker
 *
 * @ORM\Table(schema="participating", name="speaker")
 * @ORM\Entity()
 */
class Speaker
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="text", nullable=true)
     *
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_big", type="text", nullable=true)
     *
     */
    private $avatar_big;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_small", type="text", nullable=true)
     *
     */
    private $avatar_small;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="publish", type="boolean", nullable=true)
     */
    private $publish;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="organization", type="string", nullable=false)
     */
    private $organization;

    /**
     * @var Conference
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Conference", inversedBy="speakers")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id", nullable=false)
     */
    private $conference;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Speaker
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set avatar_small
     *
     * @param string $avatar
     *
     * @return Speaker
     */
    public function setAvatarSmall($avatar)
    {
        $this->avatar_small = $avatar;

        return $this;
    }

    /**
     * Get avatar_small
     *
     * @return string
     */
    public function getAvatarSmall()
    {
        return $this->avatar_small;
    }

    /**
     * Set avatar_big
     *
     * @param string $avatar
     *
     * @return Speaker
     */
    public function setAvatarBig($avatar)
    {
        $this->avatar_big = $avatar;

        return $this;
    }

    /**
     * Get avatar_big
     *
     * @return string
     */
    public function getAvatarBig()
    {
        return $this->avatar_big;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Speaker
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set publish
     *
     * @param boolean $publish
     *
     * @return Speaker
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return bool
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Conference
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * @param Conference $conference
     */
    public function setConference(Conference $conference): void
    {
        $this->conference = $conference;
    }
}