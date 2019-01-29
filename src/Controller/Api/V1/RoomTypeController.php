<?php

namespace App\Controller\Api\V1;

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
     * @Route("room_type/new", methods={"POST"})
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

        // TODO: Check maxPlaces change !

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
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var RoomType $type */
        if (!$type = $this->em->find(RoomType::class, $id)) {
            return $this->notFound('Room type not found.');
        }

        if ($type->getRooms()->count()) {
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
            'max_places'    => $roomType->getMaxPlaces(),
        ];

        return $item;
    }
}