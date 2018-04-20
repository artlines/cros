<?php

namespace AppBundle\Entity;

/**
 * SpeakerReports
 */
class SpeakerReports
{
    /**
     * @var string
     */
    private $report;

    /**
     * @var integer
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
