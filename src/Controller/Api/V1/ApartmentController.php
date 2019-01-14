<?php

namespace App\Controller\Api\V1;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ApartmentController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__apartment__")
 */
class ApartmentController extends ApiController
{
    /**
     * @Route("apartment/generate", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function generate()
    {

    }
}