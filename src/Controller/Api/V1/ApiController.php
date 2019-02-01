<?php

namespace App\Controller\Api\V1;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller\Api\V1
 */
class ApiController extends AbstractController
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var Request */
    protected $request;

    /** @var LoggerInterface */
    protected $logger;

    /** @var array */
    protected $requestData;

    /**
     * ApiController constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em       = $entityManager;
        $this->logger   = $logger;
        $this->request  = Request::createFromGlobals();

        $this->requestData = $this->request->isMethod('GET') || $this->request->isMethod('DELETE')
            ? $this->request->query->all()
            : $this->parseJsonRequest();
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function success(array $data = [])
    {
        return $this->json($data, 200);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    protected function created($id)
    {
        return $this->json(['id' => $id], 201);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $msg
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function notFound($msg = 'Resource not found.')
    {
        return $this->_error($msg, 404);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function badRequest($message = 'Not valid request content')
    {
        return $this->_error($message, 400);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param \Exception $e
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function exception(\Exception $e)
    {
        $this->logger->error('[API] File: '.$e->getFile().' | Line: '.$e->getLine().' | Message: '.$e->getMessage());
        return $this->_error('Непредвиденная ошибка. Пожалуйста, обратитесь к администратору и уточните время и действие, после которого возникла ошибка.');
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function parseJsonRequest()
    {
        $rawBody = $this->request->getContent();
        $content = json_decode($rawBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->badRequest(json_last_error_msg());
        }

        return $content;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $msg
     * @param int $code
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function _error($msg, $code = 500)
    {
        return $this->json(['error' => $msg], $code);
    }
}