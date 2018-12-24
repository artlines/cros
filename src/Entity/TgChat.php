<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Stage
 *
 * @ORM\Table(name="tgchat")
 * @ORM\Entity(repositoryClass="App\Repository\TgChatRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TgChat
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
     * @ORM\Column(name="chat_id", type="integer", unique=true, nullable=false)
     */
    private $chatId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var boolean
     *
     * @ORM\Column(name="allow_notify", type="boolean", nullable=false, options={"default": "1"})
     */
    private $allowNotify;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="joined", type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $joined;

    /**
     * Many Chats have many Lectures
     *
     * @ORM\ManyToMany(targetEntity="Lecture", inversedBy="chats")
     * @ORM\JoinTable(
     *      name="tgchat_lecture",
     *      joinColumns={
     *          @ORM\JoinColumn(name="tgchat_id", referencedColumnName="id")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="lecture_id", referencedColumnName="id")
     *      }
     *  )
     * @ORM\OrderBy({"date" = "ASC", "startTime" = "ASC"})
     */
    private $lectures;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", nullable=true)
     */
    private $state;

    /**
     * TgChat constructor.
     */
    public function __construct()
    {
        $this->lectures     = new ArrayCollection();
        $this->isActive     = true;
        $this->allowNotify  = true;
        $this->state        = json_encode(array());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @param int $chatId
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return \DateTime
     */
    public function getJoined()
    {
        return $this->joined;
    }

    /**
     * @ORM\PrePersist
     */
    public function setJoined()
    {
        $this->joined = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getLectures()
    {
        return $this->lectures;
    }

    /**
     * @param Lecture $lecture
     */
    public function addLecture($lecture)
    {
        if (!$this->lectures->contains($lecture)) {
            $this->lectures->add($lecture);
        }
    }

    /**
     * @param Lecture $lecture
     */
    public function removeLecture($lecture)
    {
        if ($this->lectures->contains($lecture)) {
            $this->lectures->removeElement($lecture);
        }
    }

    public function denyNotify()
    {
        $this->allowNotify = false;
    }

    public function allowNotify()
    {
        $this->allowNotify = true;
    }

    public function isAllowNotify()
    {
        return (boolean) $this->allowNotify;
    }

    /**
     * @return array
     */
    public function getState()
    {
        if (is_null($this->state)) {
            return array();
        }

        return json_decode($this->state, true);
    }

    /**
     * @param array $state
     */
    public function setState($state)
    {
        $this->state = json_encode($state);
    }
}

