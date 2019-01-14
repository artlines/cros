<?php

namespace App\Manager;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\ApartmentType;
use App\Entity\Abode\Housing;
use App\Entity\Abode\RoomType;
use App\Repository\Abode\ApartmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class ApartmentGenerator
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var LoggerInterface */
    protected $logger;

    /** @var ApartmentRepository */
    protected $apartmentRepository;

    /**
     * AbodeManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->em       = $entityManager;
        $this->logger   = $logger;

        $this->apartmentRepository = $this->em->getRepository(Apartment::class);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param int $num_from
     * @param int $num_to
     * @param int $floor
     * @param ApartmentType $type
     * @param Housing $housing
     * @param RoomType[] $roomTypes
     * @throws \Exception
     */
    public function generate($num_from, $num_to, $floor, ApartmentType $type, Housing $housing, $roomTypes)
    {
        // check that $num_to greater than $num_from
        if ($num_from < $num_to) {
            throw new \LogicException('Значение начального номера не может быть меньше конечного');
        }

        // check that $floor less than $housing->floors
        if ($housing->getNumOfFloors() < $floor) {
            throw new \LogicException('Значение этажа не может быть больше, чем количество этажей в корпусе');
        }

        // check that $roomTypes length === $type->max_rooms
        if (count($roomTypes) !== $type->getMaxRooms()) {
            throw new \LogicException('Переданное количество типов комнат не совпадает с количеством комнат в номере указанного типа');
        }

        // check that at least one apartment with number from interval is exist
        $existApartments = $this->apartmentRepository->getByHousingAndNumInterval($housing, $num_from, $num_to);
        if (count($existApartments) !== 0) {
            $existApartmentsNumbers = array_map(function($apartment) {
                /** @var Apartment $apartment*/
                return $apartment->getNumber();
            }, $existApartments);

            throw new \LogicException('Для данного корпуса в заданном интервале уже имеются номера: '.implode(', ', $existApartmentsNumbers));
        }

        try {
            $this->em->beginTransaction();

            for ($num = $num_from; $num <= $num_to; $num++) {
                $apartment = new Apartment();
                $apartment->setNumber($num);
                $apartment->setType($type);
                $apartment->setFloorNumber($floor);
                $apartment->setHousing($housing);

                $this->em->persist($apartment);
                $this->em->flush();
            }

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new \Exception($e);
        }
    }
}