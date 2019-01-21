<?php

namespace App\Repository;

use App\Entity\Conference;
use App\Entity\Participating\Organization;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ConferenceOrganizationRepository extends EntityRepository
{
    public function searchBy(Conference $conference, array $data = [], $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('co');

        $dql = $qb
            ->select('co')
            ->leftJoin(Organization::class, 'o', Expr\Join::WITH, 'co.organization = o');

        $parameters = [];
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'name_or_inn':
                    $dql->andWhere(
                        $qb->expr()->orX(
                            "o.name LIKE '%$value%'",
                            "o.inn LIKE '%$value%'"
                        )
                    );
                    break;
                default:
                    $dql->andWhere("co.$key = :$key");
                    $parameters[$key] = $value;
                    break;
            }
        }

        $query = $dql
            ->setParameters($parameters)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }
}