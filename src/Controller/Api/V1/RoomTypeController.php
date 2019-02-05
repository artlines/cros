<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Abode\Room;
use App\Entity\Abode\RoomType;
use App\Entity\Participating\ParticipationClass;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class RoomTypeController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__room_type__")
 */
class RoomTypeController extends ApiController
{
    /**
     * @Route("room_type", name="get_all", methods={"GET"})
     * @IsGranted("ROLE_CMS_USER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var RoomType[] $roomTypes */
        $roomTypes = $this->em->getRepository(RoomType::class)->findBy([], ['id' => 'ASC']);

        $items = [];
        foreach ($roomTypes as $roomType) {
            $items[] = $this->getResponseItem($roomType);
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @Route("room_type/new", methods={"POST"})
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function new()
    {
        $title = $this->requestData['title'] ?? null;
        $description = $this->requestData['description'] ?? null;
        $maxPlaces = $this->requestData['max_places'] ?? null;
        $cost = $this->requestData['cost'] ?? null;
        $participationClassId = $this->requestData['participation_class_id'] ?? null;

        if (!$title || !$description || !$maxPlaces || !$cost || !$participationClassId) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        /** @var ParticipationClass $class */
        if (!$class = $this->em->find(ParticipationClass::class, $participationClassId)) {
            return $this->notFound('Participation class not found.');
        }

        $type = new RoomType();
        $type->setTitle($title);
        $type->setDescription($description);
        $type->setMaxPlaces($maxPlaces);
        $type->setCost($cost);
        $type->setParticipationClass($class);

        $this->em->persist($type);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("room_type/{id}", requirements={"id":"\d+"}, methods={"PUT"})
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $title = $this->requestData['title'] ?? null;
        $description = $this->requestData['description'] ?? null;
        $maxPlaces = $this->requestData['max_places'] ?? null;
        $cost = $this->requestData['cost'] ?? null;
        $participationClassId = $this->requestData['participation_class_id'] ?? null;

        if (!$title || !$description || !$maxPlaces || !$cost || !$participationClassId) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        /** @var RoomType $type */
        if (!$type = $this->em->find(RoomType::class, $id)) {
            return $this->notFound('Room type not found.');
        }

        /** @var ParticipationClass $class */
        if (!$class = $this->em->find(ParticipationClass::class, $participationClassId)) {
            return $this->notFound('Participation class not found.');
        }

        /** Check already hold places count */
        $roomsInfo = $this->em->getRepository(Place::class)->getPlacesInfoByType($type);
        $errorMsg = '';
        foreach ($roomsInfo as $item) {
            if ($item['places_count'] > $maxPlaces) {
                $errorMsg .= "В {$item['housing_title']} в номере {$item['apartment_number']} "
                    ."занято {$item['places_count']} мест.\r\n";
            }
        }
        if ($errorMsg) {
            return $this->badRequest($errorMsg);
        }

        $type->setTitle($title);
        $type->setDescription($description);
        $type->setMaxPlaces($maxPlaces);
        $type->setCost($cost);
        $type->setParticipationClass($class);

        $this->em->persist($type);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("room_type/{id}", requirements={"id":"\d+"}, methods={"DELETE"})
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var RoomType $type */
        if (!$roomType = $this->em->find(RoomType::class, $id)) {
            return $this->notFound('Room type not found.');
        }

        if (count($roomType->getRooms())) {
            return $this->badRequest('У данного типа есть привязанные комнаты');
        }

        $this->em->remove($type);
        $this->em->flush();

        return $this->success();
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
            'description'   => $roomType->getDescription(),
            'cost'          => $roomType->getCost(),
            'max_places'    => $roomType->getMaxPlaces(),

            'participation_class_id' => $roomType->getParticipationClass()->getId(),
        ];

        return $item;
    }
}