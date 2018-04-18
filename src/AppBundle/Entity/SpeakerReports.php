<?php

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * SpeakerReports
 *
 * @ORM\Table(name="speaker_reports")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SpeakerReportsRepository")
 */
class SpeakerReports
{
    /**
     * @var string
     */
    private $report;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Speaker
     */
    private $speaker;


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
     * @param \AppBundle\Entity\Speaker $speaker
     *
     * @return SpeakerReports
     */
    public function setSpeaker(\AppBundle\Entity\Speaker $speaker = null)
    {
        $this->speaker = $speaker;

        return $this;
    }

    /**
     * Get speaker
     *
     * @return \AppBundle\Entity\Speaker
     */
    public function getSpeaker()
    {
        return $this->speaker;
    }
}
