<?php

namespace AppBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * Sponsor
 *
 * @ORM\Table(name="sponsor")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SponsorRepository")
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
     * Set report
     *
     * @param string $report
     *
     * @return SpeakerReports
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set speaker
     *
     * @param int $speaker_id
     *
     * @return SpeakerReports
     */
    public function setSpeaker($speaker_id)
    {
        $this->speaker_id = $speaker_id;

        return $this;
    }
    /*
    public function setSpeaker(\AppBundle\Entity\Speaker $speaker_id = null)
    {
        $this->speaker_id = $speaker_id;

        return $this;
    }
    */

    /**
     * Get speaker
     *
     * @return \AppBundle\Entity\Speaker
     */
    public function getSpeaker()
    {
        return $this->speaker_id;
    }
}
