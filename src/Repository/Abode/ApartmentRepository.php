<?php

namespace App\Repository\Abode;

use App\Entity\Abode\Apartment;
use App\Entity\Abode\ApartmentType;
use App\Entity\Abode\Housing;
use Doctrine\ORM\EntityRepository;

class ApartmentRepository extends EntityRepository
{
    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Housing $housing
     * @param $num_from
     * @param $num_to
     * @return Apartment[]
     */
    public function getByHousingAndNumInterval(Housing $housing, $num_from, $num_to)
    {
        $qb = $this->createQueryBuilder('a');

        $query = $qb
            ->select('a')
            ->where(
                $qb->expr()->between('a.number', ':num_from', ':num_to')
            )
            ->andWhere('a.housing = :housing')
            ->setParameters([
                'num_from'  => $num_from,
                'num_to'    => $num_to,
                'housing'   => $housing,
            ])
            ->getQuery();

        return $query->getResult();
    }

    public function countByType(ApartmentType $type)
    {
        $qb = $this->createQueryBuilder('a');

        $query = $qb
            ->select('count(a.id)')
            ->where('a.type = :type')
            ->setParameter('type', $type)
            ->getQuery();

        return $query->getSingleScalarResult();
    }
}