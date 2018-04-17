<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrgToConf
 *
 * @ORM\Table(name="org_to_conf", indexes={@ORM\Index(name="IDX_8062CCA932C8A3DE", columns={"organization_id"}), @ORM\Index(name="IDX_8062CCA9604B8382", columns={"conference_id"})})
 * @ORM\Entity
 */
class OrgToConf
{
    /**
     * @var integer
     *
     * @ORM\Column(name="paid", type="integer", nullable=true)
     */
    private $paid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="summ", type="string", length=255, nullable=true)
     */
    private $summ;

    /**
     * @var string
     *
     * @ORM\Column(name="paid_sum", type="string", length=255, nullable=true)
     */
    private $paidSum;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice", type="string", length=255, nullable=true)
     */
    private $invoice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime", nullable=true)
     */
    private $paymentDate;

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
     * @var \AppBundle\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organization_id", referencedColumnName="id")
     * })
     */
    private $organization;


}

