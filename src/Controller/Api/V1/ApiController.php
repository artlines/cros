<?php

namespace App\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller\Api\V1
 */
class ApiController extends AbstractController
{
    protected function success(array $data = [])
    {
        $code = empty($data) ? 204 : 200;

        return $this->json($data, $code);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    protected function created(array $data)
    {
        if (empty($data)) {
            throw new \Exception('Data cannot be empty.');
        }

        return $this->json($data, 201);
    }

    protected function notFound()
    {
        return $this->_error('Resource not found.', 404);
    }

    protected function _error($msg, $code = 500)
    {
        return $this->json(['error' => $msg, $code]);
    }
}