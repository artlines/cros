<?php

namespace App\Manager;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\Housing;
use App\Entity\Abode\Room;
use App\Repository\Abode\ApartmentRepository;
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

    /**
     * AbodeManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em       = $entityManager;
        $this->logger   = $logger;

        $this->housingRepository    = $this->em->getRepository(Housing::class);
        $this->apartmentRepository  = $this->em->getRepository(Apartment::class);
        $this->roomRepository       = $this->em->getRepository(Room::class);
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

        $statsByRoomTypeId = [];
        foreach ($rooms as $room) {
            $_room_type_id  = $room->getType()->getId();
            $_total_places  = $room->getType()->getMaxPlaces();
            $_busy_places   = $room->getPlaces()->count();

            if (!isset($statsByRoomTypeId[$_room_type_id])) {
                $statsByRoomTypeId[$_room_type_id] = [
                    'busy'  => 0,
                    'total' => 0,
                ];
            };

            $statsByRoomTypeId[$_room_type_id]['busy'] += $_busy_places;
            $statsByRoomTypeId[$_room_type_id]['total'] += $_total_places;
        }

        $result = [];
        foreach ($statsByRoomTypeId as $room_type_id => $stat) {
            $result[] = [
                'room_type_id'  => $room_type_id,
                'busy'          => $stat['busy'],
                'total'         => $stat['total'],
            ];
        }

        return $result;
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

                    $places[] = [
                        'id'        => $place->getId(),
                        'approved'  => $place->isApproved() ? 1 : 0,
                        'member'    => [
                            'id'            => $conferenceMember->getId(),
                            'first_name'    => $user->getFirstName(),
                            'last_name'     => $user->getLastName(),
                            'org_name'      => $user->getOrganization()->getName(),
                            'room_type_id'  => $conferenceMember->getRoomType()->getId(),
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