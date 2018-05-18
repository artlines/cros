<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Info
 *
 * @ORM\Table(name="interview")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InterviewRepository")
 */
class Interview
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
     * @ORM\Column(name="company", type="integer")
     */
    private $company;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=false)
     */
    private $name;
    /**
     * @var int
     *
     * @ORM\Column(name="visits", type="integer")
     */
    private $visits;
    /**
     * @var int
     *
     * @ORM\Column(name="qualityOrganization", type="integer")
     */
    private $qualityOrganization;
    /**
     * @var string
     *
     * @ORM\Column(name="qualityOrganizationComents", type="text", nullable=true)
     */
    private $qualityOrganizationComents;
    /**
     * @var int
     *
     * @ORM\Column(name="presentations", type="integer")
     */
    private $presentations;
    /**
     * @var string
     *
     * @ORM\Column(name="PresentationsComents", type="text", nullable=true)
     */
    private $presentationsComents;
    /**
     * @var int
     *
     * @ORM\Column(name="tables", type="integer")
     */
    private $tables;
    /**
     * @var string
     *
     * @ORM\Column(name="tablesComents", type="text", nullable=true)
     */
    private $tablesComents;
    /**
     * @var int
     *
     * @ORM\Column(name="entertainment", type="integer")
     */
    private $entertainment;
    /**
     * @var string
     *
     * @ORM\Column(name="entertainmentComents", type="text", nullable=true)
     */
    private $entertainmentComents;
    /**
     * @var string
     *
     * @ORM\Column(name="food", type="string", length=255, unique=false)
     */
    private $food;
    /**
     * @var string
     *
     * @ORM\Column(name="foodComents", type="text", nullable=true)
     */
    private $foodComents;
    /**
     * @var int
     *
     * @ORM\Column(name="search", type="integer")
     */
    private $search;
    /**
     * @var string
     *
     * @ORM\Column(name="searchComents", type="text", nullable=true)
     */
    private $searchComents;
    /**
     * @var string
     *
     * @ORM\Column(name="informationalResources", type="string", length=255, unique=false)
     */
    private $informationalResources;
    /**
     * @var string
     *
     * @ORM\Column(name="informationalResourcesComents", type="text", nullable=true)
     */
    private $informationalResourcesComents;
    /**
     * @var int
     *
     * @ORM\Column(name="whatImportant", type="integer")
     */
    private $whatImportant;
    /**
     * @var string
     *
     * @ORM\Column(name="whatImportantComent", type="text", nullable=true)
     */
    private $whatImportantComent;
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return int
     */
    public function getCompany()
    {
        return $this->company;
    }
    /**
     * @return int
     */
    public function getVisits()
    {
        return $this->visits;
    }
    /**
     * @return int
     */
    public function getQualityOrganization()
    {
        return $this->qualityOrganization;
    }
    /**
     * @return string
     */
    public function getQualityOrganizationComents()
    {
        return $this->qualityOrganizationComents;
    }
    /**
     * @return int
     */
    public function getPresentations()
    {
        return $this->presentations;
    }
    /**
     * @return string
     */
    public function getPresentationsComents()
    {
        return $this->presentationsComents;
    }
    /**
     * @return int
     */
    public function getTables()
    {
        return $this->tables;
    }
    /**
     * @return string
     */
    public function getTablesComents()
    {
        return $this->tablesComents;
    }
    /**
     * @return int
     */
    public function getEntertainment()
    {
        return $this->entertainment;
    }
    /**
     * @return string
     */
    public function getEntertainmentComents()
    {
        return $this->entertainmentComents;
    }
    /**
     * @return int
     */
    public function getFood()
    {
        return $this->food;
    }
    /**
     * @return string
     */
    public function getFoodComents()
    {
        return $this->foodComents;
    }
    /**
     * @return int
     */
    public function getSearch()
    {
        return $this->search;
    }
    /**
     * @return string
     */
    public function getSearchComents()
    {
        return $this->searchComents;
    }
    /**
     * @return string
     */
    public function getInformationalResources()
    {
        return $this->informationalResources;
    }
    /**
     * @return string
     */
    public function getInformationalResourcesComents()
    {
        return $this->informationalResourcesComents;
    }
    /**
     * @return int
     */
    public function getWhatImportant()
    {
        return $this->whatImportant;
    }
    /**
     * @return string
     */
    public function getWhatImportantComent()
    {
        return $this->whatImportantComent;
    }



    /**
     * @param string $name
     *
     * @return Interview
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @param integer $company
     *
     * @return Interview
     */
    public function setCompany($company)
    {
         $this->company = $company;
         return $this;
    }
    /**
     * @param integer $visits
     *
     * @return Interview
     */
    public function setVisits($visits)
    {
         $this->visits = $visits;
        return $this;
    }
    /**
     * @param string $qualityOrganization
     *
     * @return Interview
     */
    public function setQualityOrganization($qualityOrganization)
    {
         $this->qualityOrganization = $qualityOrganization;
        return $this;
    }
    /**
     * @param string $qualityOrganizationComents
     *
     * @return Interview
     */
    public function setQualityOrganizationComents($qualityOrganizationComents)
    {
         $this->qualityOrganizationComents = $qualityOrganizationComents;
        return $this;
    }
    /**
     * @param integer $presentations
     *
     * @return Interview
     */
    public function setPresentations($presentations)
    {
         $this->presentations = $presentations;
        return $this;
    }
    /**
     * @param string $presentationsComents
     *
     * @return Interview
     */
    public function setPresentationsComents($presentationsComents)
    {
        $this->presentationsComents = $presentationsComents;
        return $this;
    }
    /**
     * @param integer $tables
     *
     * @return Interview
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
        return $this;
    }
    /**
     * @param string $tablesComents
     *
     * @return Interview
     */
    public function setTablesComents($tablesComents)
    {
        $this->tablesComents = $tablesComents;
        return $this;
    }
    /**
     * @param integer $entertainment
     *
     * @return Interview
     */
    public function setEntertainment($entertainment)
    {
         $this->entertainment = $entertainment;
        return $this;
    }
    /**
     * @param string $entertainmentComents
     *
     * @return Interview
     */
    public function setEntertainmentComents($entertainmentComents)
    {
         $this->entertainmentComents = $entertainmentComents;
        return $this;
    }
    /**
     * @param integer $food
     *
     * @return Interview
     */
    public function setFood($food)
    {
         $this->food = $food;
        return $this;
    }
    /**
     * @param string $foodComents
     *
     * @return Interview
     */
    public function setFoodComents($foodComents)
    {
         $this->foodComents = $foodComents;
        return $this;
    }
    /**
     * @param integer $search
     *
     * @return Interview
     */
    public function setSearch($search)
    {
        $this->search = $search;
        return $this;
    }
    /**
     * @param string $searchComents
     *
     * @return Interview
     */
    public function setSearchComents($searchComents)
    {
        $this->searchComents = $searchComents;
        return $this;
    }
    /**
     * @param string $informationalResources
     *
     * @return Interview
     */
    public function setInformationalResources($informationalResources)
    {
         $this->informationalResources = $informationalResources;
        return $this;
    }
    /**
     * @param string $informationalResourcesComents
     *
     * @return Interview
     */
    public function setInformationalResourcesComents($informationalResourcesComents)
    {
         $this->informationalResourcesComents = $informationalResourcesComents;
        return $this;
    }
    /**
     * @param integer $whatImportant
     *
     * @return Interview
     */
    public function setWhatImportant($whatImportant)
    {
        $this->whatImportant = $whatImportant;
        return $this;
    }
    /**
     * @param string $whatImportantComent
     *
     * @return Interview
     */
    public function setWhatImportantComent($whatImportantComent)
    {
        $this->whatImportantComent = $whatImportantComent;
        return $this;
    }
}