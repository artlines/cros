<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InfoToConf
 *
 * @ORM\Table(name="info_to_conf", indexes={@ORM\Index(name="IDX_71D8B1F95D8BC1F8", columns={"info_id"}), @ORM\Index(name="IDX_71D8B1F9604B8382", columns={"conference_id"})})
 * @ORM\Entity
 */
class InfoToConf
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Conference
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Conference")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     * })
     */
    private $conference;

    /**
     * @var \AppBundle\Entity\Info
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Info")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="info_id", referencedColumnName="id")
     * })
     */
    private $info;


}

