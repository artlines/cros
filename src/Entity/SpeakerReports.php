<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * SpeakerReports
 *
 * @ORM\Table(name="speaker_reports")
 * @ORM\Entity(repositoryClass="App\Repository\SpeakerReportsRepository")
 */
class SpeakerReports
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
     * @ORM\Column(name="report", type="string", length=255)
     */
    private $report;
    /**
     * @var int
     *
     * @ORM\Column(name="speaker_id", type="integer")
     */
    private $speaker_id;


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
    public function setSpeaker(\App\Entity\Speaker $speaker_id = null)
    {
        $this->speaker_id = $speaker_id;

        return $this;
    }
    */

    /**
     * Get speaker
     *
     * @return \App\Entity\Speaker
     */
    public function getSpeaker()
    {
        return $this->speaker_id;
    }
}
