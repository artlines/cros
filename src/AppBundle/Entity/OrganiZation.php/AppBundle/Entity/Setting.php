<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var string
     *
     * @ORM\Column(name="footer_text", type="string", length=255, nullable=true)
     */
    private $footerText;

    /**
     * @var string
     *
     * @ORM\Column(name="forum_link", type="string", length=255, nullable=true)
     */
    private $forumLink;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_emails", type="string", length=255, nullable=true)
     */
    private $adminEmails;

    /**
     * @var string
     *
     * @ORM\Column(name="send_pass", type="string", length=255, nullable=true)
     */
    private $sendPass;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

