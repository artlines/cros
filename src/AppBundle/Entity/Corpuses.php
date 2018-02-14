<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Corpuses
 *
 * @ORM\Table(name="corpuses")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CorpusesRepository")
 */
class Corpuses
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
     * @ORM\Column(name="stage1", type="integer", nullable=true)
     */
    private $stage1;

    /**
     * @var int
     *
     * @ORM\Column(name="stage2", type="integer", nullable=true)
     */
    private $stage2;

    /**
     * @var int
     *
     * @ORM\Column(name="stage3", type="integer", nullable=true)
     */
    private $stage3;

    /**
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="corpus1")
     * @ORM\JoinColumn(name="stage1", referencedColumnName="id")
     */
    private $stages1;

    /**
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="corpus2")
     * @ORM\JoinColumn(name="stage2", referencedColumnName="id")
     */
    private $stages2;

    /**
     * @ORM\ManyToOne(targetEntity="Stage", inversedBy="corpus3")
     * @ORM\JoinColumn(name="stage3", referencedColumnName="id")
     */
    private $stages3;


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
     * Set stage1
     *
     * @param integer $stage1
     *
     * @return Corpuses
     */
    public function setStage1($stage1)
    {
        $this->stage1 = $stage1;

        return $this;
    }

    /**
     * Get stage1
     *
     * @return int
     */
    public function getStage1()
    {
        return $this->stage1;
    }

    /**
     * Set stage2
     *
     * @param integer $stage2
     *
     * @return Corpuses
     */
    public function setStage2($stage2)
    {
        $this->stage2 = $stage2;

        return $this;
    }

    /**
     * Get stage2
     *
     * @return int
     */
    public function getStage2()
    {
        return $this->stage2;
    }

    /**
     * Set stage3
     *
     * @param integer $stage3
     *
     * @return Corpuses
     */
    public function setStage3($stage3)
    {
        $this->stage3 = $stage3;

        return $this;
    }

    /**
     * Get stage3
     *
     * @return int
     */
    public function getStage3()
    {
        return $this->stage3;
    }

    /**
     * Set stages1
     *
     * @param \AppBundle\Entity\Stage $stages1
     *
     * @return Corpuses
     */
    public function setStages1(\AppBundle\Entity\Stage $stages1 = null)
    {
        $this->stages1 = $stages1;

        return $this;
    }

    /**
     * Get stages1
     *
     * @return \AppBundle\Entity\Stage
     */
    public function getStages1()
    {
        return $this->stages1;
    }

    /**
     * Set stages2
     *
     * @param \AppBundle\Entity\Stage $stages2
     *
     * @return Corpuses
     */
    public function setStages2(\AppBundle\Entity\Stage $stages2 = null)
    {
        $this->stages2 = $stages2;

        return $this;
    }

    /**
     * Get stages2
     *
     * @return \AppBundle\Entity\Stage
     */
    public function getStages2()
    {
        return $this->stages2;
    }

    /**
     * Set stages3
     *
     * @param \AppBundle\Entity\Stage $stages3
     *
     * @return Corpuses
     */
    public function setStages3(\AppBundle\Entity\Stage $stages3 = null)
    {
        $this->stages3 = $stages3;

        return $this;
    }

    /**
     * Get stages3
     *
     * @return \AppBundle\Entity\Stage
     */
    public function getStages3()
    {
        return $this->stages3;
    }
}
