<?php

namespace App\Old\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity()
 */
class Sponsor
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="bigint")
     */
    private $phone;
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;
    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=255)
     */
    private $logo;
    /**
     * @var string
     *
     * @ORM\Column(name="logo_resize", type="string", length=255)
     */
    private $logo_resize;
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    /**
     * @ORM\ManyToOne(targetEntity="TypeSponsor")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     */
    private $type;
    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;
    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="bigint")
     */
    private $priority;
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
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Get phone
     *
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }
    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }
    /**
     * Get logo_resize
     *
     * @return string
     */
    public function getLogoResize()
    {
        return $this->logo_resize;
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
     * Get type
     *
     * @return \App\Old\Entity\TypeSponsor
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Get active
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->active;
    }
    /**
     * Set name
     *
     * @param string $name
     *
     * @return Sponsor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Sponsor
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
    /**
     * Set url
     *
     * @param string $url
     *
     * @return Sponsor
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
    /**
     * Set logo
     *
     * @param string $logo
     *
     * @return Sponsor
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }
    /**
     * Set logo_resize
     *
     * @param string $logo_resize
     *
     * @return Sponsor
     */
    public function setLogoResize($logo_resize)
    {
        $this->logo_resize = $logo_resize;

        return $this;
    }
    /**
     * Set description
     *
     * @param string $description
     *
     * @return Sponsor
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
    /**
     * Set SponsorType
     *
     * @param \App\Old\Entity\TypeSponsor $TypeSponsor
     *
     * @return Sponsor
     */
    public function setTypeSponsor(\App\Old\Entity\TypeSponsor $TypeSponsor = null)
    {
        $this->type = $TypeSponsor;

        return $this;
    }
    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Sponsor
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }
    /**
     * Set priority
     *
     * @param int $priority
     *
     * @return Sponsor
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }
}
