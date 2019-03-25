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

        $this->b2bHost  = getenv('B2B_API_HOST');
        $this->b2bToken = getenv('B2B_API_TOKEN');

        if (!$this->b2bToken || !$this->b2bHost) {
            $msg = "[B2B API] Ошибка инициализации сервиса. Не установлены необходимые параметры ('B2B_API_HOST', 'B2B_API_TOKEN') .env";
            $this->logger->critical($msg);
            throw new \Exception($msg);
        }
    }

    /**
     * Create new contractor on B2B
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $title
     * @param string $inn
     * @param null $kpp
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `result`    => data or error string (if http_code !== 200)
     */
    public function createContractor(string $title, string $inn, $kpp = null)
    {
        $result = $this->_executeCurl('contractor/new', [
            'title' => $title,
            'inn'   => $inn,
            'kpp'   => $kpp,
        ], Request::METHOD_POST);

        return $result;
    }

    /**
     * Find contractor on B2B
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $inn
     * @param string|null $kpp
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `data`    => data or error string (if http_code !== 200)
     */
    public function findContractorByInnKpp(string $inn, ?string $kpp = null)
    {
        $result = $this->_executeCurl('contractor', ['inn' => $inn, 'kpp' => $kpp], Request::METHOD_GET);

        return $result;
    }

    /**
     * Find contractor on B2B
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $guid
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `data`    => data or error string (if http_code !== 200)
     */
    public function findContractorByGuid(string $guid)
    {
        $result = $this->_executeCurl("contractor/$guid", [], Request::METHOD_GET);

        return $result;
    }

    /**
     * Return contractors users fixed_guids
     *
     * @author Ivan Slyusar i.slyusar@nag.ru
     * @param $guids
     * @return array
     */
    public function getContractorsUsers($guids)
    {
        $result = $this->_executeCurl("contractors/users", $guids, Request::METHOD_POST);

        return $result;
    }

    /**
     * Return contractor users fixed_guids
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $guid
     * @return array
     */
    public function getContractorUsers($guid)
    {
        $result = $this->_executeCurl("contractor/$guid/users");

        return $result;
    }

    /**
     * Create new ties users with contractor
     *
     * @param $contractor_guid
     * @param $data array
     *      contains elements:
     *          `users_guids` - array of users B2B fixed_guids
     *
     * @return array
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function updateContractorUsers($contractor_guid, $data)
    {
        $result = $this->_executeCurl('contractor/'.$contractor_guid.'/users', $data, Request::METHOD_POST);

        return $result;
    }

    /**
     * Create new contractor on B2B
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $email
     * @param string $fio
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `result`    => data or error string (if http_code !== 200)
     */
    public function createUser(string $email, string $fio)
    {
        $result = $this->_executeCurl('user/new', [
            'email' => $email,
            'fio'   => $fio,
        ], Request::METHOD_POST);

        return $result;
    }

    /**
     * Find user on B2B
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $email
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `data`    => data or error string (if http_code !== 200)
     */
    public function findUserByEmail(string $email)
    {
        $result = $this->_executeCurl('user', ['email' => $email], Request::METHOD_GET);

        return $result;
    }

    /**
     * Create new order
     *
     * @param $data array of items:
     *      contractor_guid => fixed GUID contractor from B2B
     *      user_guid       => fixed GUID user from B2B
     *      phone           => order phone
     *      email           => order email
     *      services        => ['sku' => service SKU, 'amount' => amount of service]
     *
     * @return array
     */
    public function createOrder($data)
    {
        $result = $this->_executeCurl('order/new', $data, Request::METHOD_POST);

        return $result;
    }

    /**
     * Return orders info about invoices
     * @author Ivan Slyusar i.slyusar@nag.ru
     * @param $invoices
     * @return array
     */
    public function getOrdersInvoicesInfo($invoices)
    {
        $result = $this->_executeCurl("orders/info", $invoices, Request::METHOD_POST);

        return $result;
    }

    /**
     * Return order information
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $guid
     * @return array
     */
    public function getOrderInfo($guid)
    {
        $result = $this->_executeCurl("order/$guid");

        return $result;
    }

    /**
     * Get order invoice document
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $guid
     * @return array
     */
    public function getOrderInvoiceFile($guid)
    {
        $result = $this->_executeCurl("order/$guid/invoice", [], Request::METHOD_GET);

        return $result;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $alias
     * @param array $data
     * @param string $method
     * @return array with elements
     *      `http_code` => HTTP code of response,
     *      `data`      => data or error string
     */
    private function _executeCurl($alias, array $data = [], string $method = Request::METHOD_GET)
    {
        $url = $this->b2bHost.'/api/secure/'.$alias.($method === Request::METHOD_GET ? '?'.http_build_query($data) : '');

        $ch = curl_init();

        $curlOpts = [
            CURLOPT_URL             => $url,
            CURLOPT_HTTPHEADER      => [ self::AUTH_KEY_NAME . ": {$this->b2bToken}" ],
            CURLOPT_CUSTOMREQUEST   => $method,
            CURLOPT_POSTFIELDS      => $method === Request::METHOD_GET ? [] : http_build_query($data),
            CURLOPT_RETURNTRANSFER  => TRUE,
        ];

        curl_setopt_array($ch, $curlOpts);

        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $outputJson = json_decode($output, TRUE);
        if (isset($outputJson['error'])) {
            $outputJson = $outputJson['error'];
        }

        return ['data' => $outputJson ?? $output, 'http_code' => $httpCode];
    }
}