<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Stage
 *
 * @ORM\Table(name="tgchat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TgChatRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class TgChat
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
     */
    private $lectures;





    public function __construct()
    {
        $this->lectures = new ArrayCollection();
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
     * @param mixed $lectures
     */
    public function setLectures($lectures)
    {
        $this->lectures = $lectures;
    }

}

