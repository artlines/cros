<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectionLog
 *
 * @ORM\Table(name="connection_log")
 * @ORM\Entity()
 */
class ConnectionLog
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
     * @ORM\Column(name="sender", type="string", length=255)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="to_sms", type="string", length=255)
     */
    private $toSms;

    /**
     * @var string
     *
     * @ORM\Column(name="to_email", type="string", length=255)
     */
    private $toEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="text")
     */
    private $result;

    /**
     * @var int
     *
     * @ORM\Column(name="send_group", type="bigint")
     */
    private $sendGroup;


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
     * Set sender
     *
     * @param string $sender
     *
     * @return ConnectionLog
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set toSms
     *
     * @param string $toSms
     *
     * @return ConnectionLog
     */
    public function setToSms($toSms)
    {
        $this->toSms = $toSms;

        return $this;
    }

    /**
     * Get toSms
     *
     * @return string
     */
    public function getToSms()
    {
        return $this->toSms;
    }

    /**
     * Set toEmail
     *
     * @param string $toEmail
     *
     * @return ConnectionLog
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * Get toEmail
     *
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return ConnectionLog
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set result
     *
     * @param string $result
     *
     * @return ConnectionLog
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * Get result
     *
     * @return string
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set sendGroup
     *
     * @param integer $sendGroup
     *
     * @return ConnectionLog
     */
    public function setSendGroup($sendGroup)
    {
        $this->sendGroup = $sendGroup;

        return $this;
    }

    /**
     * Get sendGroup
     *
     * @return int
     */
    public function getSendGroup()
    {
        return $this->sendGroup;
    }
}
