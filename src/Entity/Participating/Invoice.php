<?php

namespace App\Entity\Participating;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Invoice
 * @package App\Entity\Participating
 *
 * @ORM\Table(schema="participating", name="invoice")
 * @ORM\Entity()
 */
class Invoice
{
    const STATUS__NO_PAYED      = 1; // не оплачен
    const STATUS__PARTLY_PAYED  = 2; // частично оплачен
    const STATUS__FULLY_PAYED   = 3; // полностью оплачен

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="num", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_id", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pay_date", type="date", nullable=false)
     */
    private $date;

    /**
     * GUID заказа из b2b
     *
     * @var string|null
     * @ORM\Column(name="b2b_order_guid", type="string", nullable=true, unique=true)
     */
    private $orderGuid;

    /**
     * Зафиксированная сумма заказа выставленного счета
     *
     * @var float|null
     * @ORM\Column(name="fixed_amount", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $fixedAmount;

    /**
     * Флаг, отправлен ли запрос на выставление счета
     *
     * @var bool
     * @ORM\Column(name="bill_sent", type="boolean", nullable=false, options={"default":"0"})
     */
    private $billSent;

    /**
     * @var ConferenceOrganization
     *
     * @ORM\ManyToOne(targetEntity="ConferenceOrganization", inversedBy="invoices")
     * @ORM\JoinColumn(name="conference_organization_id", referencedColumnName="id", nullable=false)
     */
    private $conferenceOrganization;

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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number)
    {
        $this->number = $number;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return ConferenceOrganization
     */
    public function getConferenceOrganization()
    {
        return $this->conferenceOrganization;
    }

    /**
     * @param ConferenceOrganization $conferenceOrganization
     */
    public function setConferenceOrganization(ConferenceOrganization $conferenceOrganization)
    {
        $this->conferenceOrganization = $conferenceOrganization;
    }

    /**
     * @return null|string
     */
    public function getOrderGuid(): ?string
    {
        return $this->orderGuid;
    }

    /**
     * @param null|string $orderGuid
     */
    public function setOrderGuid(?string $orderGuid)
    {
        $this->orderGuid = $orderGuid;
    }

    /**
     * @return float
     */
    public function getFixedAmount(): ?float
    {
        return $this->fixedAmount;
    }

    /**
     * @param float $fixedAmount
     */
    public function setFixedAmount(?float $fixedAmount)
    {
        $this->fixedAmount = $fixedAmount;
    }

    /**
     * @return bool
     */
    public function isBillSent(): bool
    {
        return $this->billSent;
    }

    /**
     * @param bool $billSent
     */
    public function setBillSent(bool $billSent)
    {
        $this->billSent = $billSent;
    }
}