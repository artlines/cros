<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Corpuses
 *
 * @ORM\Table(name="corpuses", indexes={@ORM\Index(name="IDX_225031FBB7704AD8", columns={"stage1"}), @ORM\Index(name="IDX_225031FB2E791B62", columns={"stage2"}), @ORM\Index(name="IDX_225031FB597E2BF4", columns={"stage3"})})
 * @ORM\Entity
 */
class Corpuses
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
     * @var \AppBundle\Entity\Stage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Stage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stage1", referencedColumnName="id")
     * })
     */
    private $stage1;

    /**
     * @var \AppBundle\Entity\Stage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Stage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stage3", referencedColumnName="id")
     * })
     */
    private $stage3;

    /**
     * @var \AppBundle\Entity\Stage
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Stage")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="stage2", referencedColumnName="id")
     * })
     */
    private $stage2;


}

