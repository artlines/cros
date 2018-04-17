<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * OrgToConf
 *
 * @ORM\Table(name="org_to_conf")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrgToConfRepository")
 */
class OrgToConf
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
     * @ORM\Column(name="organization_id", type="integer")
     */
    private $organizationId;

    /**
     * @var int
     *
     * @ORM\Column(name="conference_id", type="integer")
     */
    private $conferenceId;

    /**
     * @var int | 0 - не оплачено, 1 - оплачено, 2 - частично оплачено
     *
     * @ORM\Column(name="paid", type="integer",nullable=true, options={"default":"0"})
     */
    private $paid;

    /**
     * @var string
     *
     * @ORM\Column(name="summ", type="string", nullable=true)
     */
    private $summ;

    /**
     * @var string
     *
     * @ORM\Column(name="paid_sum", type="string", nullable=true)
     */
    private $paidSum;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", nullable=true)
     */
    private $invoice;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime", nullable=true)
     */
    private $paymentDate;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="otc")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     */
    private $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Conference", inversedBy="otc")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id")
     */
    private $conference;

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
     * Set organizationId
     *
     * @param integer $organizationId
     *
     * @return OrgToConf
     */
    public function setOrganizationId($organizationId)
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    /**
     * Get organizationId
     *
     * @return int
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * Set conferenceId
     *
     * @param integer $conferenceId
     *
     * @return OrgToConf
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
     * Set paid
     *
     * @param mixed $paid
     *
     * @return OrgToConf
     */
    public function setPaid($paid){

        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid
     *
     * @return integer
     */
    public function getPaid(){
        $paid = $this->paid;

        return $paid;
    }

    /**
     * Set organization
     *
     * @param \AppBundle\Entity\Organization $organization
     *
     * @return OrgToConf
     */
    public function setOrganization(\AppBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \AppBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set conference
     *
     * @param \AppBundle\Entity\Conference $conference
     *
     * @return OrgToConf
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
     * Set summ
     *
     * @param string $summ
     *
     * @return OrgToConf
     */
    public function setSumm($summ)
    {
        $this->summ = $summ;

        return $this;
    }

    /**
     * Get summ
     *
     * @return string
     */
    public function getSumm()
    {
        return $this->summ;
    }

    /**
     * Set invoice
     *
     * @param string $invoice
     *
     * @return OrgToConf
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return string
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     *
     * @return OrgToConf
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set paidSum
     *
     * @param string $paidSum
     *
     * @return OrgToConf
     */
    public function setPaidSum($paidSum)
    {
        $this->paidSum = $paidSum;

        return $this;
    }

    /**
     * Get paidSum
     *
     * @return string
     */
    public function getPaidSum()
    {
        return $this->paidSum;
    }
}
