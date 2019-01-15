<?php

namespace App\Repository\Abode;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\Housing;
use App\Entity\Abode\Place;
use App\Entity\Abode\Room;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;

class RoomRepository extends EntityRepository
{
    public function getAllEmptyRooms()
    {
        $qb = $this->createQueryBuilder('r');

        $query = $qb
            ->select('r')
            ->where('r.places IS EMPTY')
            ->getQuery();

        return $query->getResult();
    }

    public function getByHousing(Housing $housing)
    {
        $qb = $this->createQueryBuilder('r');

        $query = $qb
            ->select('r')
            ->leftJoin(Apartment::class, 'a', Expr\Join::WITH, 'r.apartment = a')
            ->leftJoin(Housing::class, 'h', Expr\Join::WITH, 'a.housing = h')
            ->where('h = :housing')
            ->setParameter('housing', $housing)
            ->getQuery()
            ->setFetchMode(Room::class, 'places', ClassMetadata::FETCH_EAGER)
            ->setFetchMode(Room::class, 'apartment', ClassMetadata::FETCH_EAGER);

        return $query->getResult();
    }
}