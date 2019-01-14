<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\ApartmentType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class RoomTypeController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__apartment_type__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ApartmentTypeController extends ApiController
{
    /**
     * @Route("apartment_type", name="get_all", methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var ApartmentType[] $apartmentTypes */
        $apartmentTypes = $this->em->getRepository(ApartmentType::class)->findAll();

        $items = [];
        foreach ($apartmentTypes as $apartmentType) {
            $items[] = $this->getResponseItem($apartmentType);
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param ApartmentType $apartmentType
     * @return array
     */
    private function getResponseItem(ApartmentType $apartmentType)
    {
        $item = [
            'id'        => $apartmentType->getId(),
            'title'     => $apartmentType->getTitle(),
            'max_rooms' => $apartmentType->getMaxRooms(),
        ];

        return $item;
    }
}