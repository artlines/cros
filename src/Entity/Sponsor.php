<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity()
 */
class Sponsor
{
    const TYPE__SILVER  = 1;
    const TYPE__GOLD    = 2;

    protected static $mapTypeTitle = [
        self::TYPE__SILVER  => 'Серебряный',
        self::TYPE__GOLD    => 'Золотой',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="decimal", precision=15)
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
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @var Conference
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Conference", inversedBy="sponsors")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id", nullable=false)
     */
    private $conference;

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

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTypeName()
    {
        return self::$mapTypeTitle[$this->type];
    }

    /**
     * @param int $type
     * @throws \Exception
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(self::$mapTypeTitle))) {
            throw new \Exception('Invalid type ID');
        }

        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param int $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return Conference
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * @param Conference $conference
     */
    public function setConference(Conference $conference): void
    {
        $this->conference = $conference;
    }
}
