<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="setting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SettingRepository")
 */
class Setting
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
     * @var string
     *
     * @ORM\Column(name="footer_text", type="string", length=255, nullable=true)
     */
    private $footer_text;

    /**
     * @var string
     *
     * @ORM\Column(name="forum_link", type="string", length=255, nullable=true)
     */
    private $forum_link;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_emails", type="string", length=255, nullable=true)
     */
    private $admin_emails;

    /**
     * @var string
     *
     * @ORM\Column(name="send_pass", type="string", length=255, nullable=true)
     */
    private $send_pass;

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
     * Set footerText
     *
     * @param string $footer_text
     *
     * @return Setting
     */
    public function setFooterText($footer_text)
    {
        $this->footer_text = $footer_text;

        return $this;
    }

    /**
     * Get footer_text
     *
     * @return string
     */
    public function getFooterText()
    {
        return $this->footer_text;
    }

    /**
     * Set forumLink
     *
     * @param string $forumLink
     *
     * @return Setting
     */
    public function setForumLink($forumLink)
    {
        $this->forum_link = $forumLink;

        return $this;
    }

    /**
     * Get forumLink
     *
     * @return string
     */
    public function getForumLink()
    {
        return $this->forum_link;
    }

    /**
     * Set adminEmails
     *
     * @param string $adminEmails
     *
     * @return Setting
     */
    public function setAdminEmails($adminEmails)
    {
        $this->admin_emails = $adminEmails;

        return $this;
    }

    /**
     * Get adminEmails
     *
     * @return string
     */
    public function getAdminEmails()
    {
        return $this->admin_emails;
    }

    /**
     * Set sendPass
     *
     * @param string $sendPass
     *
     * @return Setting
     */
    public function setSendPass($sendPass)
    {
        $this->send_pass = $sendPass;

        return $this;
    }

    /**
     * Get sendPass
     *
     * @return string
     */
    public function getSendPass()
    {
        return $this->send_pass;
    }
}
