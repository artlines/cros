<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
//use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Speaker
 *
 * @ORM\Table(name="speaker")
 * @ORM\Entity(repositoryClass="App\Repository\SpeakerRepository")
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
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     *
     * @var File
     */
    private $avatarFile;

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
     * @ORM\Column(name="image", type="text", nullable=true)
     */
    private $image;

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
     * @var integer
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;

    /**
     * @var string
     *
     * @ORM\Column(name="report", type="string", length=255, nullable=true)
     */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participating\User", inversedBy="speakers")
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
     * @return Speaker
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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Speaker
     */
    public function setAvatarFile(File $image = null){
        $this->avatarFile = $image;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getAvatarFile(){
        return $this->avatarFile;
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
     * Get avatar_small
     *
     * @return string
     */
    public function getAvatarSmall()
    {
        return $this->avatar_small;
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
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Speaker
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
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
     * Set user
     *
     * @param \App\Entity\Participating\User $user
     *
     * @return Speaker
     */
    public function setUser(\App\Entity\Participating\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\Participating\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return Speaker
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return integer
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Set report
     *
     * @param string $report
     *
     * @return Speaker
     */
    public function setReport($report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return string
     */
    public function getReport()
    {
        return $this->report;
    }
    /**
     * @var string
     */
    private $avatarBig;

    /**
     * @var string
     */
    private $avatarSmall;


}