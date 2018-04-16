<?php

namespace AppBundle\Entity;

/**
 * Tgchat
 */
class Tgchat
{
    /**
     * @var integer
     */
    private $chatId;

    /**
     * @var boolean
     */
    private $isActive;

    /**
     * @var \DateTime
     */
    private $joined = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lecture;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lecture = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set chatId
     *
     * @param integer $chatId
     *
     * @return Tgchat
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;

        return $this;
    }

    /**
     * Get chatId
     *
     * @return integer
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Tgchat
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set joined
     *
     * @param \DateTime $joined
     *
     * @return Tgchat
     */
    public function setJoined($joined)
    {
        $this->joined = $joined;

        return $this;
    }

    /**
     * Get joined
     *
     * @return \DateTime
     */
    public function getJoined()
    {
        return $this->joined;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add lecture
     *
     * @param \AppBundle\Entity\Lecture $lecture
     *
     * @return Tgchat
     */
    public function addLecture(\AppBundle\Entity\Lecture $lecture)
    {
        $this->lecture[] = $lecture;

        return $this;
    }

    /**
     * Remove lecture
     *
     * @param \AppBundle\Entity\Lecture $lecture
     */
    public function removeLecture(\AppBundle\Entity\Lecture $lecture)
    {
        $this->lecture->removeElement($lecture);
    }

    /**
     * Get lecture
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLecture()
    {
        return $this->lecture;
    }
}
