<?php

namespace App\Old\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserToApartament
 *
 * @ORM\Table(name="user_to_apartament")
 * @ORM\Entity()
 */
class UserToApartament
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
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="apartaments_id", type="integer")
     */
    private $apartamentsId;

    /**
     * @var bool
     *
     * @ORM\Column(name="approved", type="boolean")
     */
    private $approved;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="utoas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="ApartamentId", inversedBy="atoais")
     * @ORM\JoinColumn(name="apartaments_id", referencedColumnName="id")
     */
    private $apartament;


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
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserToApartament
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set apartamentsId
     *
     * @param integer $apartamentsId
     *
     * @return UserToApartament
     */
    public function setApartamentsId($apartamentsId)
    {
        $this->apartamentsId = $apartamentsId;

        return $this;
    }

    /**
     * Get apartamentsId
     *
     * @return int
     */
    public function getApartamentsId()
    {
        return $this->apartamentsId;
    }

    /**
     * Set user
     *
     * @param \App\Old\Entity\User $user
     *
     * @return UserToApartament
     */
    public function setUser(\App\Old\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Old\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set apartament
     *
     * @param \App\Old\Entity\ApartamentId $apartament
     *
     * @return UserToApartament
     */
    public function setApartament(\App\Old\Entity\ApartamentId $apartament = null)
    {
        $this->apartament = $apartament;

        return $this;
    }

    /**
     * Get apartament
     *
     * @return \App\Old\Entity\ApartamentId
     */
    public function getApartament()
    {
        return $this->apartament;
    }

    /**
     * Set approved
     *
     * @param boolean $approved
     *
     * @return UserToApartament
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean
     */
    public function getApproved()
    {
        return $this->approved;
    }
}
