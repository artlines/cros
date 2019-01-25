<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Abode\Room;
use App\Entity\Participating\ConferenceMember;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class PlaceController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__place__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class PlaceController extends ApiController
{
    /**
     * @Route("place/new", methods={"POST"}, name="new")
     */
    public function new()
    {
        $conference_member_id = $this->requestData['conference_member_id'] ?? null;
        $room_id = $this->requestData['room_id'] ?? null;

        if (!$conference_member_id && !$room_id) {
            return $this->badRequest('Require parameters not set.');
        }

        /** @var ConferenceMember $conferenceMember */
        if (!$conferenceMember = $this->em->find(ConferenceMember::class, (int) $conference_member_id)) {
            return $this->notFound('Conference member not found.');
        }

        /** @var Room $room */
        if (!$room = $this->em->find(Room::class, (int) $room_id)) {
            return $this->notFound('Room not found.');
        }

        $roomType = $room->getType();

        /** Check count of room places */
        if ($room->getPlaces()->count() >= $roomType->getMaxPlaces()) {
            return $this->badRequest('В комнате закончились места.');
        }

        /** Check that conference member chosen room type equal $room type */
        if ($conferenceMember->getRoomType() !== $roomType) {
            return $this->badRequest('Тип комнаты для заселения не соответствует типу, который указан у участника');
        }

        $place = new Place();
        $place->setRoom($room);
        $place->setConferenceMember($conferenceMember);

        $this->em->persist($place);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("place/{id}", requirements={"id":"\d+"}, methods={"DELETE"}, name="delete")
     *
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var Place $place */
        if (!$place = $this->em->find(Place::class, $id)) {
            return $this->notFound('Place not found.');
        }

        $this->em->remove($place);
        $this->em->flush();

        return $this->success();
    }
}