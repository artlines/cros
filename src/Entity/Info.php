<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Info
 *
 * @ORM\Table(name="info")
 * @ORM\Entity(repositoryClass="App\Repository\InfoRepository")
 */
class Info
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, unique=false)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\OneToMany(targetEntity="InfoToConf", mappedBy="info")
     */
    private $conftoinfos;

    public function __construct()
    {
        $this->conftoinfos = new ArrayCollection();
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
     * Set alias
     *
     * @param string $alias
     *
     * @return Info
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Info
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Info
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add conftoinfo
     *
     * @param \App\Entity\InfoToConf $conftoinfo
     *
     * @return Info
     */
    public function addConftoinfo(\App\Entity\InfoToConf $conftoinfo)
    {
        $this->conftoinfos[] = $conftoinfo;

        return $this;
    }

    /**
     * Remove conftoinfo
     *
     * @param \App\Entity\InfoToConf $conftoinfo
     */
    public function removeConftoinfo(\App\Entity\InfoToConf $conftoinfo)
    {
        $this->conftoinfos->removeElement($conftoinfo);
    }

    /**
     * Get conftoinfos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getConftoinfos()
    {
        return $this->conftoinfos;
    }
}
