<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class B2BApi
{
    const AUTH_KEY_NAME = 'B2B-AUTH';

    /**
     * B2B API parameters
     *
     * @var string $b2bHost
     * @var string $b2bToken
     */
    private $b2bHost;
    private $b2bToken;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * B2BApi constructor.
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface $logger
     * @throws \Exception
     */
    public function __construct(ParameterBagInterface $parameterBag, LoggerInterface $logger)
    {
        $this->logger = $logger;

        try {
            $this->b2bHost = $parameterBag->get('b2b_api_host');
            $this->b2bToken = $parameterBag->get('b2b_api_token');
        } catch (ParameterNotFoundException $e) {
            $msg = "[B2B API] Ошибка инициализации сервиса. Не установлены необходимые параметры ('b2b_api_host', 'b2b_api_token') в parameters.yaml";
            $this->logger->critical($msg);
            throw new \Exception($msg);
        }
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $inn
     * @param string|null $kpp
     * @return mixed
     */
    public function findContractorByInnKpp(string $inn, ?string $kpp = null)
    {
        $result = $this->_executeCurl('contractor', ['inn' => $inn, 'kpp' => $kpp], Request::METHOD_GET);

        return $result;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $alias
     * @param array $data
     * @param string $method
     * @param bool $decodeJson Decode JSON from string
     * @return mixed
     */
    private function _executeCurl($alias, array $data = [], string $method = Request::METHOD_GET, $decodeJson = TRUE)
    {
        $url = $this->b2bHost . '/api/secure/' . $alias;

        $ch = curl_init();

        $curlOpts = [
            CURLOPT_URL             => $method === Request::METHOD_GET ? $url . '?' . http_build_query($data) : $url,
            CURLOPT_HTTPHEADER      => [ self::AUTH_KEY_NAME . ": {$this->b2bToken}" ],
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $method === Request::METHOD_GET ? [] : $data,
            CURLOPT_RETURNTRANSFER  => TRUE,
        ];

        curl_setopt_array($ch, $curlOpts);
        $output = curl_exec($ch);
        curl_close($ch);

        if ($decodeJson) {
            $output = json_decode($output);
        }

        return $output;
    }
}