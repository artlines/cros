<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Flat
 *
 * @ORM\Table(name="flat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FlatRepository")
 */
class Flat
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
     * @ORM\Column(name="room1", type="integer", nullable=true)
     */
    private $room1;

    /**
     * @var int
     *
     * @ORM\Column(name="room2", type="integer", nullable=true)
     */
    private $room2;

    /**
     * @var int
     *
     * @ORM\Column(name="room3", type="integer", nullable=true)
     */
    private $room3;

    /**
     * @var int
     *
     * @ORM\Column(name="room4", type="integer", nullable=true)
     */
    private $room4;

    /**
     * @var int
     *
     * @ORM\Column(name="room5", type="integer", nullable=true)
     */
    private $room5;

    /**
     * @var int
     *
     * @ORM\Column(name="maxroom", type="integer")
     */
    private $maxroom;

    /**
     * @var int
     *
     * @ORM\Column(name="type_id", type="integer")
     */
    private $typeId;

    /**
     * @var int
     *
     * @ORM\Column(name="finished", type="integer")
     */
    private $finished;
    
    /**
     * @var int
     *
     * @ORM\Column(name="real_id", type="integer", nullable=true)
     */
    private $realId;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentType", inversedBy="flats")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="flats1")
     * @ORM\JoinColumn(name="room1", referencedColumnName="id")
     */
    private $realroom1;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="flats2")
     * @ORM\JoinColumn(name="room2", referencedColumnName="id")
     */
    private $realroom2;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="flats3")
     * @ORM\JoinColumn(name="room3", referencedColumnName="id")
     */
    private $realroom3;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="flats4")
     * @ORM\JoinColumn(name="room4", referencedColumnName="id")
     */
    private $realroom4;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="flats5")
     * @ORM\JoinColumn(name="room5", referencedColumnName="id")
     */
    private $realroom5;

    /**
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="realflat1")
     */
    private $stages1;

    /**
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="realflat2")
     */
    private $stages2;

    /**
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="realflat3")
     */
    private $stages3;

    /**
     * @ORM\OneToMany(targetEntity="Stage", mappedBy="realflat4")
     */
    private $stages4;

    /**
     * Flat constructor.
     */
    public function __construct()
    {
        $this->stages1 = new ArrayCollection();
        $this->stages2 = new ArrayCollection();
        $this->stages3 = new ArrayCollection();
        $this->stages4 = new ArrayCollection();
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
     * Set room1
     *
     * @param integer $room1
     *
     * @return Flat
     */
    public function setRoom1($room1)
    {
        $this->room1 = $room1;

        return $this;
    }

    /**
     * Get room1
     *
     * @return int
     */
    public function getRoom1()
    {
        return $this->room1;
    }

    /**
     * Set room2
     *
     * @param integer $room2
     *
     * @return Flat
     */
    public function setRoom2($room2)
    {
        $this->room2 = $room2;

        return $this;
    }

    /**
     * Get room2
     *
     * @return int
     */
    public function getRoom2()
    {
        return $this->room2;
    }

    /**
     * Set room3
     *
     * @param integer $room3
     *
     * @return Flat
     */
    public function setRoom3($room3)
    {
        $this->room3 = $room3;

        return $this;
    }

    /**
     * Get room3
     *
     * @return int
     */
    public function getRoom3()
    {
        return $this->room3;
    }

    /**
     * Set maxroom
     *
     * @param integer $maxroom
     *
     * @return Flat
     */
    public function setMaxroom($maxroom)
    {
        $this->maxroom = $maxroom;

        return $this;
    }

    /**
     * Get maxroom
     *
     * @return int
     */
    public function getMaxroom()
    {
        return $this->maxroom;
    }

    /**
     * Set typeId
     *
     * @param integer $typeId
     *
     * @return Flat
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set type
     *
     * @param \AppBundle\Entity\ApartamentType $type
     *
     * @return Flat
     */
    public function setType(\AppBundle\Entity\ApartamentType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\ApartamentType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set room4
     *
     * @param integer $room4
     *
     * @return Flat
     */
    public function setRoom4($room4)
    {
        $this->room4 = $room4;

        return $this;
    }

    /**
     * Get room4
     *
     * @return integer
     */
    public function getRoom4()
    {
        return $this->room4;
    }

    /**
     * Set room5
     *
     * @param integer $room5
     *
     * @return Flat
     */
    public function setRoom5($room5)
    {
        $this->room5 = $room5;

        return $this;
    }

    /**
     * Get room5
     *
     * @return integer
     */
    public function getRoom5()
    {
        return $this->room5;
    }

    /**
     * Set realroom1
     *
     * @param \AppBundle\Entity\ApartamentId $realroom1
     *
     * @return Flat
     */
    public function setRealroom1(\AppBundle\Entity\ApartamentId $realroom1 = null)
    {
        $this->realroom1 = $realroom1;

        return $this;
    }

    /**
     * Get realroom1
     *
     * @return \AppBundle\Entity\ApartamentId
     */
    public function getRealroom1()
    {
        return $this->realroom1;
    }

    /**
     * Set realroom2
     *
     * @param \AppBundle\Entity\ApartamentId $realroom2
     *
     * @return Flat
     */
    public function setRealroom2(\AppBundle\Entity\ApartamentId $realroom2 = null)
    {
        $this->realroom2 = $realroom2;

        return $this;
    }

    /**
     * Get realroom2
     *
     * @return \AppBundle\Entity\ApartamentId
     */
    public function getRealroom2()
    {
        return $this->realroom2;
    }

    /**
     * Set realroom3
     *
     * @param \AppBundle\Entity\ApartamentId $realroom3
     *
     * @return Flat
     */
    public function setRealroom3(\AppBundle\Entity\ApartamentId $realroom3 = null)
    {
        $this->realroom3 = $realroom3;

        return $this;
    }

    /**
     * Get realroom3
     *
     * @return \AppBundle\Entity\ApartamentId
     */
    public function getRealroom3()
    {
        return $this->realroom3;
    }

    /**
     * Set realroom4
     *
     * @param \AppBundle\Entity\ApartamentId $realroom4
     *
     * @return Flat
     */
    public function setRealroom4(\AppBundle\Entity\ApartamentId $realroom4 = null)
    {
        $this->realroom4 = $realroom4;

        return $this;
    }

    /**
     * Get realroom4
     *
     * @return \AppBundle\Entity\ApartamentId
     */
    public function getRealroom4()
    {
        return $this->realroom4;
    }

    /**
     * Set realroom5
     *
     * @param \AppBundle\Entity\ApartamentId $realroom5
     *
     * @return Flat
     */
    public function setRealroom5(\AppBundle\Entity\ApartamentId $realroom5 = null)
    {
        $this->realroom5 = $realroom5;

        return $this;
    }

    /**
     * Get realroom5
     *
     * @return \AppBundle\Entity\ApartamentId
     */
    public function getRealroom5()
    {
        return $this->realroom5;
    }

    /**
     * Set finished
     *
     * @param integer $finished
     *
     * @return Flat
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return integer
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Add stages1
     *
     * @param \AppBundle\Entity\Stage $stages1
     *
     * @return Flat
     */
    public function addStages1(\AppBundle\Entity\Stage $stages1)
    {
        $this->stages1[] = $stages1;

        return $this;
    }

    /**
     * Remove stages1
     *
     * @param \AppBundle\Entity\Stage $stages1
     */
    public function removeStages1(\AppBundle\Entity\Stage $stages1)
    {
        $this->stages1->removeElement($stages1);
    }

    /**
     * Get stages1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages1()
    {
        return $this->stages1;
    }

    /**
     * Add stages2
     *
     * @param \AppBundle\Entity\Stage $stages2
     *
     * @return Flat
     */
    public function addStages2(\AppBundle\Entity\Stage $stages2)
    {
        $this->stages2[] = $stages2;

        return $this;
    }

    /**
     * Remove stages2
     *
     * @param \AppBundle\Entity\Stage $stages2
     */
    public function removeStages2(\AppBundle\Entity\Stage $stages2)
    {
        $this->stages2->removeElement($stages2);
    }

    /**
     * Get stages2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages2()
    {
        return $this->stages2;
    }

    /**
     * Add stages3
     *
     * @param \AppBundle\Entity\Stage $stages3
     *
     * @return Flat
     */
    public function addStages3(\AppBundle\Entity\Stage $stages3)
    {
        $this->stages3[] = $stages3;

        return $this;
    }

    /**
     * Remove stages3
     *
     * @param \AppBundle\Entity\Stage $stages3
     */
    public function removeStages3(\AppBundle\Entity\Stage $stages3)
    {
        $this->stages3->removeElement($stages3);
    }

    /**
     * Get stages3
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages3()
    {
        return $this->stages3;
    }

    /**
     * Add stages4
     *
     * @param \AppBundle\Entity\Stage $stages4
     *
     * @return Flat
     */
    public function addStages4(\AppBundle\Entity\Stage $stages4)
    {
        $this->stages4[] = $stages4;

        return $this;
    }

    /**
     * Remove stages4
     *
     * @param \AppBundle\Entity\Stage $stages4
     */
    public function removeStages4(\AppBundle\Entity\Stage $stages4)
    {
        $this->stages4->removeElement($stages4);
    }

    /**
     * Get stages4
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStages4()
    {
        return $this->stages4;
    }

    /**
     * Set realId
     *
     * @param integer $realId
     *
     * @return Flat
     */
    public function setRealId($realId)
    {
        $this->realId = $realId;

        return $this;
    }

    /**
     * Get realId
     *
     * @return integer
     */
    public function getRealId()
    {
        return $this->realId;
    }
}
