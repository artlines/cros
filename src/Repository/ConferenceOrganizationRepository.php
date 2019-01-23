<?php

namespace App\Repository;

use App\Entity\Conference;
use App\Entity\Participating\Organization;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ConferenceOrganizationRepository extends EntityRepository
{
    public function searchBy(Conference $conference, array $data = [])
    {
        $limit = 10;
        $offset = null;
        $parameters = [];
        $qb = $this->createQueryBuilder('co');

        $dql = $qb
            ->select('co')
            ->leftJoin(Organization::class, 'o', Expr\Join::WITH, 'co.organization = o');

        $dql->andWhere('co.conference = :conference');
        $parameters['conference'] = $conference;

        /** Check for search string */
        if (isset($data['search'])) {
            $val = $data['search'];
            $dql->andWhere(
                $qb->expr()->orX(
                    "o.name LIKE '%$val%'",
                    "o.inn LIKE '%$val%'"
                )
            );
        }

        if (isset($data['invite_by'])) {
            $dql->andWhere('co.invited_by = :invited_by');

        }

        /** Check for limit and offset */
        if (isset($data['@limit'])) {
            $limit = (int) $data['@limit'];
        }
        if (isset($data['@offset'])) {
            $offset = (int) $data['@offset'];
        }

        $query = $dql
            ->addOrderBy('co.id', 'ASC')
            ->setParameters($parameters);

        $queryC = clone $query;

        $result = [
            $query->setMaxResults($limit)->setFirstResult($offset)->getQuery()->getResult(),
            count($queryC->getQuery()->getArrayResult()),
        ];

        return $result;
    }
}