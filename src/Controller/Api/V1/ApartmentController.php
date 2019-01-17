<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\ApartmentType;
use App\Entity\Abode\Housing;
use App\Entity\Abode\RoomType;
use App\Manager\ApartmentGenerator;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ApartmentController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__apartment__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ApartmentController extends ApiController
{
    /**
     * @Route("apartment", name="get_all", methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getAll()
    {
        $housing_id = $this->requestData['housing'] ?? null;
        /** @var Housing $housing */
        if (!$housing_id || (!$housing = $this->em->find(Housing::class, $housing_id))) {
            return $this->notFound('Housing not found.');
        }

        /** @var Apartment[] $apartments */
        $apartments = $this->em->getRepository(Apartment::class)
            ->findBy(['housing' => $housing], ['floorNumber' => 'ASC', 'number' => 'ASC']);

        $items = [];
        foreach ($apartments as $apartment) {
            $items[] = $this->getResponseItem($apartment);
        }

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("apartment/generate", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param ApartmentGenerator $ag
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function generate(ApartmentGenerator $ag)
    {
        $housingRepo = $this->em->getRepository(Housing::class);
        $apartmentTypeRepo = $this->em->getRepository(ApartmentType::class);
        $roomTypeRepo = $this->em->getRepository(RoomType::class);

        /** @var Housing $housing */
        if (!$housing = $housingRepo->find($this->requestData['housing_id'])) {
            return $this->notFound('Housing not found.');
        }

        /** @var ApartmentType $apartmentType */
        if (!$apartmentType = $apartmentTypeRepo->find($this->requestData['type'])) {
            return $this->notFound('Apartment type not found.');
        }

        /** @var RoomType[] $roomTypes */
        $roomTypes = [];
        foreach ($this->requestData['room_types'] as $room_type_id) {
            if (!$roomType = $roomTypeRepo->find($room_type_id)) {
                return $this->notFound('Room type not found.');
            }
            $roomTypes[] = $roomType;
        }

        try {
            $ag->generate(
                (int) $this->requestData['num_from'],
                (int) $this->requestData['num_to'],
                (int) $this->requestData['floor'],
                $apartmentType,
                $housing,
                $roomTypes
            );
        } catch (\LogicException $e) {
            return $this->badRequest($e->getMessage());
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        return $this->success();
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Apartment $apartment
     * @param bool $withRoomsInfo
     * @return array
     */
    private function getResponseItem(Apartment $apartment, $withRoomsInfo = false)
    {
        $rooms_info = [];

        $item = [
            'id'        => $apartment->getId(),
            'number'    => $apartment->getNumber(),
            'floor'     => $apartment->getFloorNumber(),
            'type'      => $apartment->getType()->getId(),
        ];

        return $item;
    }
}