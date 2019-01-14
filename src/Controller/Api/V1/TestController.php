<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\Housing;
use App\Repository\Abode\ApartmentRepository;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends ApiController
{
    /**
     * @Route("/api/v1/test")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function test()
    {
        /** @var Housing $housing */
        $housing = $this->em->getRepository(Housing::class)->find(26);

        /** @var ApartmentRepository $apartmentRepo */
        $apartmentRepo = $this->em->getRepository(Apartment::class);

        $res = $apartmentRepo->getByHousingAndNumInterval($housing, 1, 3);
        dump($res);
    }
}