<?php

namespace App\Controller\Api\V1;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UsersController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/users", name="api_v1__users_")
 */
class UsersController extends ApiController
{
    /**
     * @Route("/me", name="me", methods={"GET"})
     * @IsGranted('ROLE_CMS_USER')
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function me()
    {
        return $this->success([]);
    }
}