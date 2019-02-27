<?php

namespace App\Entity\Participating;

use App\Entity\Conference;
use App\Entity\Participating\Organization;
use App\Validator\InnKpp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ConferenceOrganization
 * @package App\Entity\Participating
 *
 * @ORM\Table(schema="participating", name="conference_organization")
 * @ORM\Entity(repositoryClass="App\Repository\ConferenceOrganizationRepository")
 */
class ConferenceOrganization
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Participating\Organization",cascade={"persist"})
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    private $organization;

    /**
     * @var Conference
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Conference")
     * @ORM\JoinColumn(name="conference_id", referencedColumnName="id", nullable=false)
     */
    private $conference;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sponsor", type="boolean", nullable=false)
     */
    private $sponsor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="finish", type="boolean", nullable=false, options={"default": 0})
     */
    private $finish;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="need_invoice", type="boolean", nullable=false, options={"default": 0})
     */
    private $needInvoice;

    /**
     * @var ArrayCollection|Comment[]
     *
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="conferenceOrganization", cascade={"remove"})
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $comments;

    /**
     * @var ArrayCollection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="conferenceOrganization")
     */
    private $invoices;

    /**
     * @var ArrayCollection|ConferenceMember[]
     *
     * @ORM\OneToMany(targetEntity="ConferenceMember", mappedBy="conferenceOrganization",cascade={"persist"})
     */
    private $conferenceMembers;

    /**
     * @var string
     *
     * @ORM\Column(name="invite_hash", type="string", nullable=true)
     */
    private $inviteHash;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="invited_by", referencedColumnName="id", nullable=true)
     */
    private $invitedBy;

    /**
     * @var array|null
     *
     * @ORM\Column(name="invite_data", type="array", nullable=true)
     */
    private $inviteData;

    /**
     * ConferenceOrganization constructor.
     */
    public function __construct()
    {
        $this->createdAt    = new \DateTime();
        $this->needInvoice  = false;
        $this->sponsor      = false;
        $this->finish       = false;
        $this->comments     = new ArrayCollection();
        $this->invoices     = new ArrayCollection();

        $this->conferenceMembers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param Organization $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Conference
     */
    public function getConference()
    {
        return $this->conference;
    }

    /**
     * @param Conference $conference
     */
    public function setConference($conference)
    {
        $this->conference = $conference;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return bool
     */
    public function isSponsor()
    {
        return $this->sponsor;
    }

    /**
     * @param bool $sponsor
     */
    public function setSponsor($sponsor)
    {
        $this->sponsor = $sponsor;
    }

    /**
     * @return bool
     */
    public function isFinish()
    {
        return $this->finish;
    }

    /**
     * @param bool $finish
     */
    public function setFinish($finish)
    {
        $this->finish = $finish;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Comment $comment
     */
    public function addComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
        }
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return Invoice[]|ArrayCollection
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Invoice $invoice
     */
    public function addInvoice(Invoice $invoice)
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
        }
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Invoice $invoice
     */
    public function removeInvoice(Invoice $invoice)
    {
        $this->invoices->removeElement($invoice);
    }

    /**
     * @return ConferenceMember[]|ArrayCollection
     */
    public function getConferenceMembers()
    {
        return $this->conferenceMembers;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param ConferenceMember $conferenceMember
     */
    public function addConferenceMember(ConferenceMember $conferenceMember)
    {
        if (!$this->conferenceMembers->contains($conferenceMember)) {
            $this->conferenceMembers->add($conferenceMember);
            $conferenceMember->setConferenceOrganization($this);
        }
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param ConferenceMember $conferenceMember
     */
    public function removeConferenceMember(ConferenceMember $conferenceMember)
    {
        $this->conferenceMembers->removeElement($conferenceMember);
        $conferenceMember->setConferenceOrganization($this);
    }

    /**
     * @return bool
     */
    public function isNeedInvoice(): bool
    {
        return $this->needInvoice;
    }

    /**
     * @param bool $needInvoice
     */
    public function setNeedInvoice(bool $needInvoice)
    {
        $this->needInvoice = $needInvoice;
    }

    /**
     * @return string|null
     */
    public function getInviteHash()
    {
        return $this->inviteHash;
    }

    /**
     * @param string|null $inviteHash
     */
    public function setInviteHash($inviteHash)
    {
        $this->inviteHash = $inviteHash;
    }

    /**
     * @return User|null
     */
    public function getInvitedBy()
    {
        return $this->invitedBy;
    }

    /**
     * @param User|null $invitedBy
     */
    public function setInvitedBy($invitedBy)
    {
        $this->invitedBy = $invitedBy;
    }

    public function __toString(){
	return 'ConferenceOrganization::'.$this->getId();
    }

    /**
     * @Assert\Callback
     * По совокупности ИНН/КПП проводить валидацию - вдруг такая организация уже зарегистрирована.
     * Уведомлять пользователя о дубле и наименовании зарегистрированной организации.
     * Валидацию проводить только по организациям, завершившим регистрацию
     * (participating.conference_organization.is_finish по conference_id за текущий год).
     */

    public function validate_inn_registered(ExecutionContextInterface $context, $payload)
    {
        return;
        /** @var ConferenceOrganization $ConferenceOrganization */
        $ConferenceOrganization =  $context->getValue();
        $inn = $ConferenceOrganization->getOrganization()->getInn();
        $kpp = $ConferenceOrganization->getOrganization()->getKpp();

        $context->buildViolation('validate_inn_registered :!'.$ConferenceOrganization->getOrganization()->getInn())
            ->atPath('organization.inn')
            ->addViolation();
        $context->buildViolation('validate_inn_registered :!'.$ConferenceOrganization->getOrganization()->getInn())
            ->atPath('organization.kpp')
            ->addViolation();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
//        $metadata->addConstraint(new Assert\Callback('validate'));
//        $metadata->addPropertyConstraint('comments', new InnKpp());
        $metadata->addConstraint( new InnKpp());

    }

    /**
     * @return array|null
     */
    public function getInviteData(): ?array
    {
        return $this->inviteData;
    }

    /**
     * @param array|null $inviteData
     */
    public function setInviteData(?array $inviteData): void
    {
        $this->inviteData = $inviteData;
    }

}