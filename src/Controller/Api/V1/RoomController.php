<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Housing;
use App\Entity\Abode\Room;
use App\Entity\Abode\RoomType;
use App\Manager\RoomConverter;
use App\Repository\Abode\RoomRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RoomController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__room__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class RoomController extends ApiController
{
    /**
     * @Route("room", name="get", methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getAll()
    {
        /** @var RoomRepository $roomRepository */
        $roomRepository = $this->em->getRepository(Room::class);
        $housingRepository = $this->em->getRepository(Housing::class);

        /** Find by housing */
        if (isset($this->requestData['housing'])) {
            /** @var Housing $housing */
            if (!$housing = $housingRepository->find((int) $this->requestData['housing'])) {
                return $this->notFound('Housing not found.');
            }

            $rooms = $roomRepository->getByHousing($housing);

            $items = [];
            foreach ($rooms as $room) {
                $items[] = $this->getResponseItem($room);
            }

            return $this->success(['items' => $items]);
        }

        return $this->badRequest();
    }

    /**
     * @Route("room/convert", name="convert", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param RoomConverter $rc
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function convert(RoomConverter $rc)
    {
        $roomsIds = $this->requestData['rooms'] ?? null;
        if (!$roomsIds || !is_array($roomsIds)) {
            return $this->badRequest('Rooms not set as array.');
        }

        /** @var Room[] $rooms */
        $rooms = array_map(function ($roomId) {
            /** @var Room $room */
            if (!$room = $this->em->find(Room::class, $roomId)) {
                return $this->notFound('Room with id '.$roomId.' not found.');
            };
            return $room;
        }, $roomsIds);

        $roomTypeId = $this->requestData['room_type'] ?? null;
        if (!$roomTypeId) {
            return $this->badRequest('Room type not set.');
        }

        /** @var RoomType $roomType */
        if (!$roomType = $this->em->find(RoomType::class, (int) $roomTypeId)) {
            return $this->notFound('Room type not found');
        }

        try {
            $rc->convert($rooms, $roomType);
        } catch (\LogicException $e) {
            return $this->badRequest($e->getMessage());
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        return $this->success();
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Room $room
     * @return array
     */
    private function getResponseItem(Room $room)
    {
        $places = [];
        foreach ($room->getPlaces() as $place) {
            $places[] = [
                'id'    => $place->getId(),
            ];
        }

        $item = [
            'id'            => $room->getId(),
            'type'          => $room->getType()->getId(),
            'apartment'     => $room->getApartment()->getId(),
            'apartment_num' => $room->getApartment()->getNumber(),
            'max_places'    => $room->getType()->getMaxPlaces(),
            'places'        => $places
        ];

        return $item;
    }
}