<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tgchat
 *
 * @ORM\Table(name="tgchat", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_4EBA689F1A9A7125", columns={"chat_id"})})
 * @ORM\Entity
 */
class Tgchat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="chat_id", type="integer", nullable=false)
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
     * @ORM\Column(name="joined", type="datetime", nullable=false)
     */
    private $joined = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Lecture", inversedBy="tgchat")
     * @ORM\JoinTable(name="tgchat_lecture",
     *   joinColumns={
     *     @ORM\JoinColumn(name="tgchat_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="lecture_id", referencedColumnName="id")
     *   }
     * )
     */
    private $lecture;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lecture = new \Doctrine\Common\Collections\ArrayCollection();
    }

}

