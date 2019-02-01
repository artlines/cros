<?php

namespace App\Old\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ManagerGroup
 *
 * @ORM\Table(name="manager_group")
 * @ORM\Entity()
 */
class ManagerGroup
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="text", nullable=true)
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="text", nullable=false)
     */
    private $hash;

    /**
     * @ORM\OneToMany(targetEntity="Organization", mappedBy="managers")
     */
    private $managed;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="managerGroup")
     */
    private $managers;

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
     * Set title
     *
     * @param string $title
     *
     * @return ManagerGroup
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     *
     * @return ManagerGroup
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return ManagerGroup
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->managed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->managers = new ArrayCollection();
    }

    /**
     * Add managed
     *
     * @param \App\Old\Entity\Organization $managed
     *
     * @return ManagerGroup
     */
    public function addManaged(\App\Old\Entity\Organization $managed)
    {
        $this->managed[] = $managed;

        return $this;
    }

    /**
     * Remove managed
     *
     * @param \App\Old\Entity\Organization $managed
     */
    public function removeManaged(\App\Old\Entity\Organization $managed)
    {
        $this->managed->removeElement($managed);
    }

    /**
     * Get managed
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManaged()
    {
        return $this->managed;
    }

    /**
     * Add manager
     *
     * @param \App\Old\Entity\User $manager
     *
     * @return ManagerGroup
     */
    public function addManager(\App\Old\Entity\User $manager)
    {
        $this->managers[] = $manager;

        return $this;
    }

    /**
     * Remove manager
     *
     * @param \App\Old\Entity\User $manager
     */
    public function removeManager(\App\Old\Entity\User $manager)
    {
        $this->managers->removeElement($manager);
    }

    /**
     * Get managers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getManagers()
    {
        return $this->managers;
    }
}
