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

    /** @var array Attachments */
    private $attachments;

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

        $this->attachments = [];
    }

    /**
     * @param mixed $templateAlias
     */
    public function setTemplateAlias($templateAlias): void
    {
        $this->templateAlias = $templateAlias;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $file
     * @param null|string $originalName
     */
    public function addAttachment($file, ?string $originalName = null)
    {
        if (is_string($file)) {
            if (!is_file($file)) {
                return;
            }

            $pathInfo = pathinfo($file);

            $attachment = [
                'data_base64' => base64_encode(file_get_contents($file)),
                'filename'    => $originalName ?? $pathInfo['basename'],
                'contentType' => (new \finfo)->file($file, FILEINFO_MIME_TYPE)
            ];
        } elseif (is_array($file) && isset($file['data_base64'], $file['filename'], $file['contentType'])) {
            $attachment = $file;
        } else {
            return;
        }

        $this->attachments[] = $attachment;
    }

    /**
     * Clear attachments array
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function clearAttachments()
    {
        $this->attachments = [];
    }

    /**
     *
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @param string $subject
     * @param array $params
     * @param string $sendTo
     * @param null|string|array $sendCc
     * @param null|string|array $sendBcc
     *
     * @return mixed
     */
    public function send(string $subject, array $params, string $sendTo, $sendCc = null, $sendBcc = null)
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
            'attachments'       => $this->attachments,
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