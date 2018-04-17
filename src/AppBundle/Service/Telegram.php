<?php

namespace AppBundle\Service;

use TelegramErrorLogger;

class Telegram extends \Telegram
{
    private function sendAPIRequest($url, array $content, $post = true)
    {
        if (isset($content['chat_id'])) {
            $url = $url.'?chat_id='.$content['chat_id'];
            unset($content['chat_id']);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // ADD socks5 settings
        $proxy = "e.nachuychenko:fSNJlcgOY@proxy.nag.ru:50000";
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $result = json_encode(['ok'=>false, 'curl_error_code' => curl_errno($ch), 'curl_error' => curl_error($ch)]);
        }
        curl_close($ch);
        if (class_exists('TelegramErrorLogger')) {
            $loggerArray = ($this->getData() == null) ? [$content] : [$this->getData(), $content];
            TelegramErrorLogger::log(json_decode($result, true), $loggerArray);
        }

        return $result;
    }
}