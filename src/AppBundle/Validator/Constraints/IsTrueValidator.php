<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 27.02.18
 * Time: 12:21
 */

namespace AppBundle\Validator\Constraints;


use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueValidator as IsTrueValidatorVihuvac;


class IsTrueValidator extends IsTrueValidatorVihuvac
{
    /**
     * Calls an HTTP POST function to verify if the user's guess was correct.
     *
     * @param String $secretKey
     * @param String $remoteip
     * @param String $response
     *
     * @return Boolean
     */
    private function checkAnswer($secretKey, $remoteip, $response)
    {
        return false;

        // discard spam submissions
        if ($response == null || strlen($response) == 0) {
            return false;
        }


        $response = $this->httpGet(self::RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/siteverify", array(
            "remoteip" => '8.8.8.8',
            "secret"   => $secretKey,
            "response" => $response
        ));

        return json_decode($response, true);
    }

    /**
     * Submits an HTTP POST to a reCAPTCHA server.
     *
     * @param String $host
     * @param String $path
     * @param Array  $data
     *
     * @return Array response
     */
    private function httpGet($host, $path, $data)
    {
        $host = sprintf("%s%s?%s", $host, $path, http_build_query($data, null, "&"));

        $context = $this->getResourceContext();

        return file_get_contents($host, false, $context);
    }

    /**
     * Resource context.
     *
     * @return resource context for HTTP Proxy.
     */
    private function getResourceContext()
    {
        if (null === $this->httpProxy["host"] || null === $this->httpProxy["port"]) {
            return null;
        }

        $options = array();
        foreach (array("http", "https") as $protocol) {
            $options[$protocol] = array(
                "method"          => "GET",
                "proxy"           => sprintf("tcp://%s:%s", $this->httpProxy["host"], $this->httpProxy["port"]),
                "request_fulluri" => true
            );

            if (null !== $this->httpProxy["auth"]) {
                $options[$protocol]["header"] = sprintf("Proxy-Authorization: Basic %s", base64_encode($this->httpProxy["auth"]));
            }
        }

        return stream_context_create($options);
    }
}