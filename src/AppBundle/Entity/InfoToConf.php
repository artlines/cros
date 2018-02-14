<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InfoToConf
 *
 * @ORM\Table(name="info_to_conf")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InfoToConfRepository")
 */
class InfoToConf
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
     * @var int
     *
     * @ORM\Column(name="info_id", type="integer")
     */
    private $infoId;

    /**
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="infotoconfs")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

    /**
     * @ORM\ManyToOne(targetEntity="Info", inversedBy="conftoinfos")
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id")
     */
    private $info;


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
     * Set infoId
     *
     * @param integer $infoId
     *
     * @return InfoToConf
     */
    public function setInfoId($infoId)
    {
        $this->infoId = $infoId;

        return $this;
    }

    /**
     * Get infoId
     *
     * @return int
     */
    public function getInfoId()
    {
        return $this->infoId;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return InfoToConf
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;

        return $this;
    }

    /**
     * Get conferenceId
     *
     * @return int
     */
    public function getConferenceId()
    {
        return $this->conferenceId;
    }

    /**
     * Set conference
     *
     * @param \AppBundle\Entity\Conference $conference
     *
     * @return InfoToConf
     */
    public function setConference(\AppBundle\Entity\Conference $conference = null)
    {
        $this->conference = $conference;

        return $this;
    }

    /**
     * Get conference
     *
     * @return \AppBundle\Entity\Conference
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * Set info
     *
     * @param \AppBundle\Entity\Info $info
     *
     * @return InfoToConf
     */
    public function setInfo(\AppBundle\Entity\Info $info = null)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Get info
     *
     * @return \AppBundle\Entity\Info
     */
    public function getInfo()
    {
        return $this->info;
    }
}
