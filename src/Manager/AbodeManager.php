<?php

namespace App\Manager;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\Housing;
use App\Entity\Abode\ReservedPlaces;
use App\Entity\Abode\Room;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\Invoice;
use App\Repository\Abode\ApartmentRepository;
use App\Repository\Abode\ReservedPlacesRepository;
use App\Repository\Abode\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;

class AbodeManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var LoggerInterface */
    protected $logger;

    /** @var EntityRepository */
    protected $housingRepository;

    /** @var ApartmentRepository */
    protected $apartmentRepository;

    /** @var RoomRepository */
    protected $roomRepository;

    /** @var ReservedPlacesRepository */
    protected $reservedPlacesRepo;

    /**
     * AbodeManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em       = $entityManager;
        $this->logger   = $logger;

        $this->roomRepository       = $this->em->getRepository(Room::class);
        $this->housingRepository    = $this->em->getRepository(Housing::class);
        $this->apartmentRepository  = $this->em->getRepository(Apartment::class);
        $this->reservedPlacesRepo   = $this->em->getRepository(ReservedPlaces::class);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Housing $housing
     * @return array
     */
    public function calculateAbodeInfoByHousing(Housing $housing)
    {
        /** @var Room[] $rooms */
        $rooms = $this->roomRepository->getByHousing($housing);
        $reservedInfo = $this->reservedPlacesRepo->getReservedInfo($housing->getId());

        $statsByRoomTypeId = [];
        foreach ($rooms as $room) {
            $_room_type_id      = $room->getType()->getId();
            $_room_type_title   = $room->getType()->getTitle();
            $_total_places      = $room->getType()->getMaxPlaces();
            $_populated_places  = $room->getPlaces()->count();

            if (!isset($statsByRoomTypeId[$_room_type_id])) {
                $statsByRoomTypeId[$_room_type_id] = [
                    'room_type_title'   => $_room_type_title,
                    'reserved'          => $reservedInfo[$_room_type_id] ?? 0,
                    'populated'         => 0,
                    'total'             => 0,
                ];
            };

            $statsByRoomTypeId[$_room_type_id]['populated'] += $_populated_places;
            $statsByRoomTypeId[$_room_type_id]['total'] += $_total_places;
        }

        return $statsByRoomTypeId;
    }

    /**
     * Calculate resettlement info to build resettlement interface in CMS
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Housing $housing
     * @return array
     */
    public function calculateResettlementByHousing(Housing $housing)
    {
        $result = [];

        foreach ($housing->getApartments() as $apartment) {
            $item = [
                'id'        => $apartment->getId(),
                'number'    => $apartment->getNumber(),
                'type_id'   => $apartment->getType()->getId(),
                'rooms'     => [],
            ];

            foreach ($apartment->getRooms() as $room) {
                $places = [];

                foreach ($room->getPlaces() as $place) {
                    $conferenceMember = $place->getConferenceMember();

                    $user = $conferenceMember->getUser();
                    $neighbourhood = $conferenceMember->getNeighbourhood();
                    $roomType = $conferenceMember->getRoomType();

                    $invoices = $conferenceMember->getConferenceOrganization()->getInvoices();
                    $invoices_payed = true;
                    foreach ($invoices as $invoice) {
                        if ($invoice->getStatus() !== Invoice::STATUS__FULLY_PAYED) {
                            $invoices_payed = false;
                        }
                    }

                    $places[] = [
                        'id'        => $place->getId(),
                        'room_id'   => $room->getId(),
                        'member'    => [
                            'id'            => $conferenceMember->getId(),
                            'first_name'    => $user->getFirstName(),
                            'last_name'     => $user->getLastName(),
                            'org_name'      => $user->getOrganization()->getName(),
                            'room_type_id'  => $roomType ? $roomType->getId() : null,
                            'neighbourhood' => $neighbourhood ? $neighbourhood->getUser()->getFullName() : null,
                            'invoices_count'=> $invoices->count(),
                            'invoices_payed'=> $invoices_payed,
                        ],
                    ];
                }

                $item['rooms'][] = [
                    'id'        => $room->getId(),
                    'type_id'   => $room->getType()->getId(),
                    'places'    => $places,
                ];
            }

            $result[] = $item;
        }

        return $result;

    }
}