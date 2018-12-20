<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stage
 *
 * @ORM\Table(name="stage")
 * @ORM\Entity()
 */
class Stage
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
     * @ORM\Column(name="flat1", type="integer", nullable=true)
     */
    private $flat1;

    /**
     * @var int
     *
     * @ORM\Column(name="flat2", type="integer", nullable=true)
     */
    private $flat2;

    /**
     * @var int
     *
     * @ORM\Column(name="flat3", type="integer", nullable=true)
     */
    private $flat3;

    /**
     * @var int
     *
     * @ORM\Column(name="flat4", type="integer", nullable=true)
     */
    private $flat4;

    /**
     * @var int
     *
     * @ORM\Column(name="max_flat", type="integer")
     */
    private $maxFlat;

    /**
     * @ORM\OneToMany(targetEntity="Corpuses", mappedBy="stages1")
     */
    private $corpus1;

    /**
     * @ORM\OneToMany(targetEntity="Corpuses", mappedBy="stages2")
     */
    private $corpus2;

    /**
     * @ORM\OneToMany(targetEntity="Corpuses", mappedBy="stages3")
     */
    private $corpus3;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="stages1")
     * @ORM\JoinColumn(name="flat1", referencedColumnName="id")
     */
    private $realflat1;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="stages2")
     * @ORM\JoinColumn(name="flat2", referencedColumnName="id")
     */
    private $realflat2;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="stages3")
     * @ORM\JoinColumn(name="flat3", referencedColumnName="id")
     */
    private $realflat3;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="stages4")
     * @ORM\JoinColumn(name="flat4", referencedColumnName="id")
     */
    private $realflat4;

    /**
     * Stage constructor.
     */
    public function __construct()
    {
        $this->corpus1 = new ArrayCollection();
        $this->corpus2 = new ArrayCollection();
        $this->corpus3 = new ArrayCollection();
    }

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
     * Set flat1
     *
     * @param integer $flat1
     *
     * @return Stage
     */
    public function setFlat1($flat1)
    {
        $this->flat1 = $flat1;

        return $this;
    }

    /**
     * Get flat1
     *
     * @return int
     */
    public function getFlat1()
    {
        return $this->flat1;
    }

    /**
     * Set flat2
     *
     * @param integer $flat2
     *
     * @return Stage
     */
    public function setFlat2($flat2)
    {
        $this->flat2 = $flat2;

        return $this;
    }

    /**
     * Get flat2
     *
     * @return int
     */
    public function getFlat2()
    {
        return $this->flat2;
    }

    /**
     * Set flat3
     *
     * @param integer $flat3
     *
     * @return Stage
     */
    public function setFlat3($flat3)
    {
        $this->flat3 = $flat3;

        return $this;
    }

    /**
     * Get flat3
     *
     * @return int
     */
    public function getFlat3()
    {
        return $this->flat3;
    }

    /**
     * Set flat4
     *
     * @param integer $flat4
     *
     * @return Stage
     */
    public function setFlat4($flat4)
    {
        $this->flat4 = $flat4;

        return $this;
    }

    /**
     * Get flat4
     *
     * @return int
     */
    public function getFlat4()
    {
        return $this->flat4;
    }

    /**
     * Set maxFlat
     *
     * @param integer $maxFlat
     *
     * @return Stage
     */
    public function setMaxFlat($maxFlat)
    {
        $this->maxFlat = $maxFlat;

        return $this;
    }

    /**
     * Get maxFlat
     *
     * @return int
     */
    public function getMaxFlat()
    {
        return $this->maxFlat;
    }

    /**
     * Add corpus1
     *
     * @param \App\Entity\Corpuses $corpus1
     *
     * @return Stage
     */
    public function addCorpus1(\App\Entity\Corpuses $corpus1)
    {
        $this->corpus1[] = $corpus1;

        return $this;
    }

    /**
     * Remove corpus1
     *
     * @param \App\Entity\Corpuses $corpus1
     */
    public function removeCorpus1(\App\Entity\Corpuses $corpus1)
    {
        $this->corpus1->removeElement($corpus1);
    }

    /**
     * Get corpus1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCorpus1()
    {
        return $this->corpus1;
    }

    /**
     * Add corpus2
     *
     * @param \App\Entity\Corpuses $corpus2
     *
     * @return Stage
     */
    public function addCorpus2(\App\Entity\Corpuses $corpus2)
    {
        $this->corpus2[] = $corpus2;

        return $this;
    }

    /**
     * Remove corpus2
     *
     * @param \App\Entity\Corpuses $corpus2
     */
    public function removeCorpus2(\App\Entity\Corpuses $corpus2)
    {
        $this->corpus2->removeElement($corpus2);
    }

    /**
     * Get corpus2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCorpus2()
    {
        return $this->corpus2;
    }

    /**
     * Add corpus3
     *
     * @param \App\Entity\Corpuses $corpus3
     *
     * @return Stage
     */
    public function addCorpus3(\App\Entity\Corpuses $corpus3)
    {
        $this->corpus3[] = $corpus3;

        return $this;
    }

    /**
     * Remove corpus3
     *
     * @param \App\Entity\Corpuses $corpus3
     */
    public function removeCorpus3(\App\Entity\Corpuses $corpus3)
    {
        $this->corpus3->removeElement($corpus3);
    }

    /**
     * Get corpus3
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCorpus3()
    {
        return $this->corpus3;
    }

    /**
     * Set realflat1
     *
     * @param \App\Entity\Flat $realflat1
     *
     * @return Stage
     */
    public function setRealflat1(\App\Entity\Flat $realflat1 = null)
    {
        $this->realflat1 = $realflat1;

        return $this;
    }

    /**
     * Get realflat1
     *
     * @return \App\Entity\Flat
     */
    public function getRealflat1()
    {
        return $this->realflat1;
    }

    /**
     * Set realflat2
     *
     * @param \App\Entity\Flat $realflat2
     *
     * @return Stage
     */
    public function setRealflat2(\App\Entity\Flat $realflat2 = null)
    {
        $this->realflat2 = $realflat2;

        return $this;
    }

    /**
     * Get realflat2
     *
     * @return \App\Entity\Flat
     */
    public function getRealflat2()
    {
        return $this->realflat2;
    }

    /**
     * Set realflat3
     *
     * @param \App\Entity\Flat $realflat3
     *
     * @return Stage
     */
    public function setRealflat3(\App\Entity\Flat $realflat3 = null)
    {
        $this->realflat3 = $realflat3;

        return $this;
    }

    /**
     * Get realflat3
     *
     * @return \App\Entity\Flat
     */
    public function getRealflat3()
    {
        return $this->realflat3;
    }

    /**
     * Set realflat4
     *
     * @param \App\Entity\Flat $realflat4
     *
     * @return Stage
     */
    public function setRealflat4(\App\Entity\Flat $realflat4 = null)
    {
        $this->realflat4 = $realflat4;

        return $this;
    }

    /**
     * Get realflat4
     *
     * @return \App\Entity\Flat
     */
    public function getRealflat4()
    {
        return $this->realflat4;
    }
}
