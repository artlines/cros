<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Logs
 *
 * @ORM\Table(name="logs")
 * @ORM\Entity
 */
class Logs
{
    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=255, nullable=false)
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(name="event", type="text", nullable=false)
     */
    private $event;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="element_id", type="integer", nullable=true)
     */
    private $elementId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="readed", type="boolean", nullable=false)
     */
    private $readed;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

