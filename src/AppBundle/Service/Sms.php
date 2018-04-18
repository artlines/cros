<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use GuzzleHttp\Exception\RequestException;
use \SimpleXMLElement;
use AppBundle\Entity\Logs;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Sms
{
    /**
     * Zanzara API URL
     *
     * @var string
     */
    private $url = 'https://www.contentum-it.ru/xml/';

    /**
     * Login
     *
     * @var string
     */
    private $login = 'MSU_SHOPNAGRU';

    /**
     * Password
     *
     * @var string
     */
    private $password = 'dihf9831';

    /**
     * SMS source_number field
     *
     * @var string
     */
    private $source_number = 'KPOC NAG.RU';

    /**
     * Messages
     *
     * @var array
     */
    private $messages = array();

    /**
     * Logs
     *
     * @var array
     */
    private $logs = array();

    /**
     * XML which ready for send
     *
     * @var string
     */
    private $xml = '';

    /** @var EntityManager */
    private $em;

    /**
     * Initiator Entity Class
     *
     * @var string
     */
    private $entityClass;

    /**
     * Initiator Entity Id
     *
     * @var integer
     */
    private $entityId;

    /**
     * Sms constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * Send
     *
     * @return mixed
     * @throws \Exception
     */
    public function send()
    {
        $this->prepareToSend();

        $ch = curl_init();

        $header = [
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Charset: UTF-8"
        ];

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);

        $result = curl_exec($ch);

        if ($result) {
            /** @var Logs $log */
            foreach ($this->logs as $log) {
                $this->em->persist($log);
            }
            $this->em->flush();
        }

        if ($errno = curl_errno($ch)) {
            throw new ResourceNotFoundException("Curl ($errno): ".curl_strerror($errno));
        }

        curl_close($ch);

        return $result;
    }

    /**
     * Prepare array of messages to readyToSendXML
     *
     * @throws \Exception
     */
    private function prepareToSend()
    {
        $messages = $this->messages;

        if (empty($messages)) {
            throw new \Exception("SMS Service: Messages array is empty. Nothing to send.");
        }

        /**
         * Create XML
         *
         * @var \SimpleXMLElement $xml
         */
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
                                            <xml_request name=\"sms_send\"></xml_request>");

        /**
         * Authorization
         */
        $auth = $xml->addChild('xml_user');
        $auth->addAttribute('lgn', $this->login);
        $auth->addAttribute('pwd', $this->password);

        /**
         * Generate SMS
         */
        foreach ($messages as $msg) {
            $sms = $xml->addChild('sms', $msg['text']);
            $sms->addAttribute('sms_id', $msg['id']);
            $sms->addAttribute('number', $msg['dst_number']);
            $sms->addAttribute('source_number', $this->source_number);
        }

        $this->xml = $xml->asXML();
    }

    /**
     * Add message
     *
     * @param $id           string
     * @param $dst_number   string
     * @param $text         string
     *
     * @return array
     */
    public function addMessage($id, $dst_number, $text)
    {
        $msg = [
            'id'        => $id,
            'dst_number'=> $dst_number,
            'text'      => $text
        ];
        $this->messages[$id] = $msg;

        return $msg;
    }

    /**
     * @param $entityClass string
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @param $entityId integer
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    }
}