<?php

namespace App\Manager;

use App\Entity\Abode\Room;
use App\Entity\Abode\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RoomConverter
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * RoomConverter constructor.
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em       = $em;
        $this->logger   = $logger;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Room[] $rooms
     * @param RoomType $type
     * @throws \Exception
     */
    public function convert($rooms, RoomType $type)
    {
        foreach ($rooms as $room) {
            if (!$room->getPlaces()->isEmpty()) {
                throw new \LogicException('Комната с ID '.$room->getId().' не пуста');
            }
        }

        $this->em->beginTransaction();

        try {
            foreach ($rooms as $room) {
                $room->setType($type);
                $this->em->persist($room);
                $this->em->flush();
            }

            $this->em->commit();
        } catch (\Exception $e) {
            $this->em->rollback();
            throw new \Exception($e);
        }
    }
}