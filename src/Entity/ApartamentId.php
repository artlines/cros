<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ApartamentId
 *
 * @ORM\Table(name="apartament_id")
 * @ORM\Entity(repositoryClass="App\Repository\ApartamentIdRepository")
 */
class ApartamentId
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
     * @ORM\Column(name="apartament_id", type="integer")
     */
    private $apartamentId;

    /**
     * @ORM\OneToMany(targetEntity="UserToApartament", mappedBy="apartament")
     */
    private $atoais;

    /**
     * @ORM\ManyToOne(targetEntity="Apartament", inversedBy="aitoas")
     * @ORM\JoinColumn(name="apartament_id", referencedColumnName="id")
     */
    private $apartament;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="realroom1")
     */
    private $flats1;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="realroom2")
     */
    private $flats2;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="realroom3")
     */
    private $flats3;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="realroom4")
     */
    private $flats4;

    /**
     * @ORM\OneToMany(targetEntity="Flat", mappedBy="realroom5")
     */
    private $flats5;

    /**
     * ApartamentId constructor.
     */
    public function __construct()
    {
        $this->atoais = new ArrayCollection();
        $this->flats1 = new ArrayCollection();
        $this->flats2 = new ArrayCollection();
        $this->flats3 = new ArrayCollection();
        $this->flats4 = new ArrayCollection();
        $this->flats5 = new ArrayCollection();
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
     * Set apartamentId
     *
     * @param integer $apartamentId
     *
     * @return ApartamentId
     */
    public function setApartamentId($apartamentId)
    {
        $this->apartamentId = $apartamentId;

        return $this;
    }

    /**
     * Get apartamentId
     *
     * @return int
     */
    public function getApartamentId()
    {
        return $this->apartamentId;
    }

    /**
     * Add atoai
     *
     * @param \App\Entity\UserToApartament $atoai
     *
     * @return ApartamentId
     */
    public function addAtoai(\App\Entity\UserToApartament $atoai)
    {
        $this->atoais[] = $atoai;

        return $this;
    }

    /**
     * Remove atoai
     *
     * @param \App\Entity\UserToApartament $atoai
     */
    public function removeAtoai(\App\Entity\UserToApartament $atoai)
    {
        $this->atoais->removeElement($atoai);
    }

    /**
     * Get atoais
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAtoais()
    {
        return $this->atoais;
    }

    /**
     * Set apartament
     *
     * @param \App\Entity\Apartament $apartament
     *
     * @return ApartamentId
     */
    public function setApartament(\App\Entity\Apartament $apartament = null)
    {
        $this->apartament = $apartament;

        return $this;
    }

    /**
     * Get apartament
     *
     * @return \App\Entity\Apartament
     */
    public function getApartament()
    {
        return $this->apartament;
    }

    /**
     * Add flats1
     *
     * @param \App\Entity\Flat $flats1
     *
     * @return ApartamentId
     */
    public function addFlats1(\App\Entity\Flat $flats1)
    {
        $this->flats1[] = $flats1;

        return $this;
    }

    /**
     * Remove flats1
     *
     * @param \App\Entity\Flat $flats1
     */
    public function removeFlats1(\App\Entity\Flat $flats1)
    {
        $this->flats1->removeElement($flats1);
    }

    /**
     * Get flats1
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats1()
    {
        return $this->flats1;
    }

    /**
     * Add flats2
     *
     * @param \App\Entity\Flat $flats2
     *
     * @return ApartamentId
     */
    public function addFlats2(\App\Entity\Flat $flats2)
    {
        $this->flats2[] = $flats2;

        return $this;
    }

    /**
     * Remove flats2
     *
     * @param \App\Entity\Flat $flats2
     */
    public function removeFlats2(\App\Entity\Flat $flats2)
    {
        $this->flats2->removeElement($flats2);
    }

    /**
     * Get flats2
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats2()
    {
        return $this->flats2;
    }

    /**
     * Add flats3
     *
     * @param \App\Entity\Flat $flats3
     *
     * @return ApartamentId
     */
    public function addFlats3(\App\Entity\Flat $flats3)
    {
        $this->flats3[] = $flats3;

        return $this;
    }

    /**
     * Remove flats3
     *
     * @param \App\Entity\Flat $flats3
     */
    public function removeFlats3(\App\Entity\Flat $flats3)
    {
        $this->flats3->removeElement($flats3);
    }

    /**
     * Get flats3
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats3()
    {
        return $this->flats3;
    }

    /**
     * Add flats4
     *
     * @param \App\Entity\Flat $flats4
     *
     * @return ApartamentId
     */
    public function addFlats4(\App\Entity\Flat $flats4)
    {
        $this->flats4[] = $flats4;

        return $this;
    }

    /**
     * Remove flats4
     *
     * @param \App\Entity\Flat $flats4
     */
    public function removeFlats4(\App\Entity\Flat $flats4)
    {
        $this->flats4->removeElement($flats4);
    }

    /**
     * Get flats4
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats4()
    {
        return $this->flats4;
    }

    /**
     * Add flats5
     *
     * @param \App\Entity\Flat $flats5
     *
     * @return ApartamentId
     */
    public function addFlats5(\App\Entity\Flat $flats5)
    {
        $this->flats5[] = $flats5;

        return $this;
    }

    /**
     * Remove flats5
     *
     * @param \App\Entity\Flat $flats5
     */
    public function removeFlats5(\App\Entity\Flat $flats5)
    {
        $this->flats5->removeElement($flats5);
    }

    /**
     * Get flats5
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFlats5()
    {
        return $this->flats5;
    }
}
