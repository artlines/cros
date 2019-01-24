<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\RoomType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class RoomTypeController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__room_type__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class RoomTypeController extends ApiController
{
    /**
     * @Route("room_type", name="get_all", methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var RoomType[] $roomTypes */
        $roomTypes = $this->em->getRepository(RoomType::class)->findAll();

        $items = [];
        foreach ($roomTypes as $roomType) {
            $items[] = $this->getResponseItem($roomType);
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param RoomType $roomType
     * @return array
     */
    private function getResponseItem(RoomType $roomType)
    {
        $item = [
            'id'            => $roomType->getId(),
            'title'         => $roomType->getTitle(),
            'max_places'    => $roomType->getMaxPlaces(),
        ];

        return $item;
    }
}