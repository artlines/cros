<?php

namespace App\Entity\Participating;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(schema="participating", name="member")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const SEX__MAN      = 1;
    const SEX__WOMAN    = 2;

    protected static $sexChoices = [
        self::SEX__MAN      => 'Мужчина',
        self::SEX__WOMAN    => 'Женщина',
    ];

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="middle_name", type="string", length=255, nullable=true)
     */
    private $middleName;

    /**
     * @var string
     *
     * @ORM\Column(name="post", type="string", length=255, nullable=true)
     */
    private $post;

    /**
     * @var int
     *
     * @ORM\Column(name="phone", type="decimal", precision=15)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false, options={"default": 1})
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="telegram", type="string", length=255, nullable=true, unique=true)
     */
    private $telegram;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="string", length=255, nullable=false, unique=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="nickname", type="string", length=255, nullable=true, unique=false)
     */
    private $nickname;

    /**
     * @var integer
     *
     * @ORM\Column(name="sex", type="integer", nullable=false)
     */
    private $sex;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="users")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", nullable=false)
     */
    private $organization;

    /**
     * Флаг представителя организации
     *
     * @var bool
     *
     * @ORM\Column(name="representative", type="boolean", options={"default": 0})
     */
    private $representative;

    /**
     * Fixed GUID пользователя из b2b
     * @var string|null
     * @ORM\Column(name="b2b_guid", type="string", nullable=true, unique=true)
     */
    private $b2b_guid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    private $entityName = 'user';

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var array
     *
     * @ORM\Column(name="additional_emails", type="json", options={"default":"[]"}, nullable=false)
     */
    private $additionalEmails;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->representative   = false;
        $this->isActive         = true;
        $this->createdAt        = new \DateTime();
        $this->roles            = json_encode(["ROLE_USER"]);
        $this->additionalEmails = json_encode([]);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     * @return User
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set post
     *
     * @param string $post
     * @return User
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return string
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return int
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param int $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get username
     *
     * @return integer
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set telegram
     *
     * @param string $telegram
     * @return User
     */
    public function setTelegram($telegram)
    {
        $this->telegram = $telegram;

        return $this;
    }

    /**
     * Get telegram
     *
     * @return string
     */
    public function getTelegram()
    {
        return $this->telegram;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return User
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;

        return $this;
    }

    /**
     * Get nickname
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Get salt
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles){
        $this->roles = json_encode($roles);

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return json_decode($this->roles, 1);
    }

    public function eraseCredentials()
    {

    }

    /**
     * Set organization
     *
     * @param Organization $organization
     *
     * @return User
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Get entityName
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * Set sex
     *
     * @param integer $sex
     *
     * @return User
     * @throws \Exception
     */
    public function setSex($sex)
    {
        if (!in_array($sex, array_keys(self::$sexChoices))) {
            throw new \Exception("Not valid sex type");
        }

        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return integer|null
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFullName()
    {
        return $this->lastName . ' ' . $this->firstName;
    }

    /**
     * @return bool
     */
    public function isRepresentative(): bool
    {
        return $this->representative;
    }

    /**
     * @param bool $representative
     */
    public function setRepresentative(bool $representative)
    {
        $this->representative = $representative;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return "user_show::".$this->getId();
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewPhoto(): ?string
    {
        return null;
    }

    /**
     * @param mixed
     * @return self
     */
    public function setNewPhoto( $Photo)
    {
        return $this;
    }

    /**
     * @return null|string
     */
    public function getB2bGuid(): ?string
    {
        return $this->b2b_guid;
    }

    /**
     * @param null|string $b2b_guid
     */
    public function setB2bGuid(?string $b2b_guid)
    {
        $this->b2b_guid = $b2b_guid;
    }

    /**
     * @return array
     */
    public function getAdditionalEmails(): array
    {
        return $this->additionalEmails;
    }

    /**
     * @param array $additionalEmails
     */
    public function setAdditionalEmails(array $additionalEmails)
    {
        $this->additionalEmails = $additionalEmails;
    }

}
