<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ManagerGroup
 *
 * @ORM\Table(name="manager_group", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_441F84FF2B36786B", columns={"title"})})
 * @ORM\Entity
 */
class ManagerGroup
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="text", length=65535, nullable=true)
     */
    private $contacts;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="text", nullable=false)
     */
    private $hash;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

