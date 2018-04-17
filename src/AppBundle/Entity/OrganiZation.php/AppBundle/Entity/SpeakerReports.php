<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpeakerReports
 *
 * @ORM\Table(name="speaker_reports", indexes={@ORM\Index(name="speaker_id", columns={"speaker_id"})})
 * @ORM\Entity
 */
class SpeakerReports
{
    /**
     * @var string
     *
     * @ORM\Column(name="report", type="string", length=255, nullable=false)
     */
    private $report;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Speaker
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Speaker")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="speaker_id", referencedColumnName="id")
     * })
     */
    private $speaker;


}

