<?php

namespace App\Entity\Participating;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Comment
 * @package App\Entity\Participating
 *
 * @ORM\Table(schema="participating", name="comment")
 * @ORM\Entity()
 */
class Comment
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
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_private", type="boolean", nullable=false, options={"default": 0})
     */
    private $isPrivate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var ConferenceOrganization
     *
     * @ORM\ManyToOne(targetEntity="ConferenceOrganization", inversedBy="comments")
     * @ORM\JoinColumn(name="conference_organization_id", referencedColumnName="id", nullable=false)
     */
    private $conferenceOrganization;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->isPrivate = false;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param bool $isPrivate
     */
    public function setIsPrivate(bool $isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Comment
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return ConferenceOrganization
     */
    public function getConferenceOrganization()
    {
        return $this->conferenceOrganization;
    }

    /**
     * @param ConferenceOrganization $conferenceOrganization
     * @return Comment
     */
    public function setConferenceOrganization(ConferenceOrganization $conferenceOrganization): self
    {
        $this->conferenceOrganization = $conferenceOrganization;
        return $this;
    }
}