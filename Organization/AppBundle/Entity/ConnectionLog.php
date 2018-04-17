<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectionLog
 *
 * @ORM\Table(name="connection_log")
 * @ORM\Entity
 */
class ConnectionLog
{
    /**
     * @var string
     *
     * @ORM\Column(name="sender", type="string", length=255, nullable=false)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="to_sms", type="string", length=255, nullable=false)
     */
    private $toSms;

    /**
     * @var string
     *
     * @ORM\Column(name="to_email", type="string", length=255, nullable=false)
     */
    private $toEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", length=65535, nullable=false)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="result", type="text", length=65535, nullable=false)
     */
    private $result;

    /**
     * @var integer
     *
     * @ORM\Column(name="send_group", type="bigint", nullable=false)
     */
    private $sendGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

