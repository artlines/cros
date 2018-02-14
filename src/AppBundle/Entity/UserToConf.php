<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserToConf
 *
 * @ORM\Table(name="user_to_conf")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserToConfRepository")
 */
class UserToConf
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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="utocs")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserToConf
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return UserToConf
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return int
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserToConf
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
