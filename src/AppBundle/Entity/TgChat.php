<?php


namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Stage
 *
 * @ORM\Table(name="tg_chat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TgChatRepository")
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
     * @ORM\Column(name="chat_id", type="integer")
     */
    private $chatId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * Many Chats have many Lectures
     *
     * @ORM\ManyToMany(targetEntity="Lecture", inversedBy="chats")
     * @ORM\JoinTable(name="chats_lectures")
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
    public function isActive()
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



}