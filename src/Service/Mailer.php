<?php

namespace App\Service;

class Mailer
{
    /** URL to send */
    private $sendUrl;

    /** Client alias */
    private $clientAlias;

    /** Client secret */
    private $clientSecret;

    /** Template alias */
    private $templateAlias;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        $this->checkEnv();

        $this->sendUrl          = getenv('SOA_MAILER_SEND');
        $this->clientAlias      = getenv('SOA_MAILER_CLIENT_ALIAS');
        $this->clientSecret     = getenv('SOA_MAILER_CLIENT_SECRET');
        $this->templateAlias    = getenv('SOA_MAILER_TEMPLATE_ALIAS');
    }

    /**
     *
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @param string $subject
     * @param array $params
     * @param string $sendTo
     * @param null|string $sendCc
     * @param null|string $sendBcc
     *
     * @return mixed
     */
    public function send(string $subject, array $params, string $sendTo, ?string $sendCc = null, ?string $sendBcc = null)
    {
        $timestamp = time();

        $query = [
            'client_alias'      => $this->clientAlias,
            'hash'              => hash('sha256', $this->clientSecret . $timestamp . $this->clientAlias),
            'subject'           => $subject,
            'send_to'           => $sendTo,
            'template_alias'    => $this->templateAlias,
            'timestamp'         => $timestamp,
            'params'            => $params,
        ];

        if ($sendCc) {
            $query['send_cc'] = $sendCc;
        }

        if ($sendBcc) {
            $query['send_bcc'] = $sendBcc;
        }

        $ch = curl_init($this->sendUrl);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Check env variables for service working
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    protected function checkEnv()
    {
        $requireVars = [
            'SOA_MAILER_SEND',
            'SOA_MAILER_CLIENT_ALIAS',
            'SOA_MAILER_CLIENT_SECRET',
            'SOA_MAILER_TEMPLATE_ALIAS',
        ];

        foreach ($requireVars as $var) {
            if (!getenv($var)) {
                    throw new \LogicException("[Mailer] Required variable $var not set in .env");
            }
        }
    }
}