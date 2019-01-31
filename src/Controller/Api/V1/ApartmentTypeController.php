<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\ApartmentType;
use App\Entity\Abode\Room;
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
        $apartmentTypes = $this->em->getRepository(ApartmentType::class)->findBy([], ['id' => 'ASC']);

        $items = [];
        foreach ($apartmentTypes as $apartmentType) {
            $items[] = $this->getResponseItem($apartmentType);
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @Route("apartment_type/new", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function new()
    {
        $title = $this->requestData['title'] ?? null;
        $maxRooms = $this->requestData['max_rooms'] ?? null;
        $code = $this->requestData['code'] ?? null;

        if (!$title || !$maxRooms || !$code) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        $type = new ApartmentType();
        $type->setTitle($title);
        $type->setCode($code);
        $type->setMaxRooms($maxRooms);

        $this->em->persist($type);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("apartment_type/{id}", requirements={"id":"\d+"}, methods={"PUT"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $title = $this->requestData['title'] ?? null;
        $maxRooms = $this->requestData['max_rooms'] ?? null;
        $code = $this->requestData['code'] ?? null;

        if (!$title || !$maxRooms || !$code) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        /** @var ApartmentType $type */
        if (!$type = $this->em->find(ApartmentType::class, $id)) {
            return $this->badRequest('Apartment type not found.');
        }

        if ($type->getMaxRooms() !== $maxRooms && $this->em->getRepository(Apartment::class)->countByType($type)) {
            return $this->badRequest('Для изменения количества комнат нужно отвязать существующие номера от данного типа');
        }

        $type->setTitle($title);
        $type->setMaxRooms($maxRooms);
        $type->setCode($code);

        $this->em->persist($type);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("apartment_type/{id}", requirements={"id":"\d+"}, methods={"DELETE"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var ApartmentType $type */
        if (!$type = $this->em->find(ApartmentType::class, $id)) {
            return $this->badRequest('Apartment type not found.');
        }

        if ($type->getApartments()->count()) {
            return $this->badRequest('К данному типу привязаны номера.');
        }

        $this->em->remove($type);
        $this->em->flush();

        return $this->success();
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
            'code'      => $apartmentType->getCode(),
        ];

        return $item;
    }
}