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
        $limit = 100;
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

        if (isset($data['invited_by'])) {
            $dql->andWhere('co.invitedBy IN (:invited_by)');
            $parameters['invited_by'] = $data['invited_by'];
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


    public function findByInnKppIsFinish($inn, $kpp, $conference_id)
    {
        return $this
            ->createQueryBuilder('co')
            ->leftJoin(Organization::class, 'o',"WITH",'co.organization=o.id')
            ->where('o.inn = :inn')
            ->andWhere('o.kpp = :kpp')
            ->andWhere('co.finish = true')
            ->andWhere('co.conference = :conference_id')
            ->setParameters([
                'inn' => $inn,
                'kpp' => $kpp,
                'conference_id' => $conference_id,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}