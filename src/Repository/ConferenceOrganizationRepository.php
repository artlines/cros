<?php

namespace App\Repository;

use App\Entity\Abode\Place;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ConferenceOrganizationRepository extends EntityRepository
{
    public function findToMakeInvoice(int $year)
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            SELECT
                   organization_id,
                   conference_organization_id,
                   contractor_guid,
                   b2b_order_guid,
                   invoice_amount,
                   SUM(fresh_amount) as fresh_amount
            FROM (
                 SELECT
                       po.id AS organization_id,
                       pi.b2b_order_guid,
                       pi.amount as invoice_amount,
                       RANK() OVER (PARTITION BY pi.conference_organization_id ORDER BY pi.created_at DESC) as invoice_rnk,
                       SUM(art.cost) as fresh_amount,
                       pco.id AS conference_organization_id,
                       po.b2b_guid as contractor_guid
                FROM participating.conference_organization pco
                  LEFT JOIN participating.organization po ON (pco.organization_id = po.id)
                  LEFT JOIN public.conference pc ON (pco.conference_id = pc.id)
                  LEFT JOIN participating.conference_member pcm ON (pco.id = pcm.conference_organization_id)
                  LEFT JOIN abode.room_type art ON pcm.room_type_id = art.id
                  LEFT JOIN participating.member pm ON pcm.user_id = pm.id AND pm.representative = TRUE
                  LEFT JOIN abode.place ap ON (pcm.id = ap.conference_member_id)
                  LEFT JOIN participating.invoice pi ON pi.conference_organization_id = pco.id
                WHERE pc.year = :year AND po.invalid_inn_kpp = FALSE AND po.b2b_guid IS NOT NULL
                GROUP BY po.id, pco.id, pi.id, pm.id, pm.b2b_guid, pm.phone, pm.email
                HAVING COUNT(ap.id) FILTER (WHERE ap.id IS NOT NULL) = COUNT(pcm.id) AND COUNT(pcm.id) != 0
                ORDER BY po.id
              ) t
            WHERE invoice_rnk = 1
            GROUP BY organization_id, conference_organization_id, contractor_guid, invoice_amount, b2b_order_guid
            HAVING invoice_amount is NULL OR SUM(fresh_amount) != invoice_amount
        ");

        $stmt->execute(['year' => $year]);

        return $stmt->fetchAll();
    }

    /**
     * Find conference organizations by conference year
     * and where invalidInnKpp is not TRUE
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param int $year
     * @return ConferenceOrganization[]
     */
    public function findWhereInnKppNotInvalidAndB2bGuidExist(int $year)
    {
        $qb = $this->createQueryBuilder('co');

        $parameters = [
            'year'          => $year,
            'invalidInnKpp' => FALSE,
        ];

        $query = $qb
            ->select('co')
            ->leftJoin(Organization::class, 'o', Expr\Join::WITH, 'co.organization = o')
            ->leftJoin(Conference::class, 'c', Expr\Join::WITH, 'co.conference = c')
            ->where('c.year = :year')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->eq('o.invalidInnKpp', ':invalidInnKpp'),
                $qb->expr()->isNull('o.invalidInnKpp')
            ))
            ->andWhere($qb->expr()->isNotNull('o.b2b_guid'));

        return $query->setParameters($parameters)->getQuery()->getResult();
    }

    /**
     * Search conference organizations by conference, invited_by and search string
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Conference $conference
     * @param array $data
     * @return array
     */
    public function searchBy(Conference $conference, array $data = [])
    {
        $limit = 100;
        $offset = null;
        $parameters = [];
        $qb = $this->createQueryBuilder('co');

        $dql = $qb
            ->select('co')
            ->leftJoin(Organization::class, 'o', Expr\Join::WITH, 'co.organization = o')
            ->leftJoin(ConferenceMember::class, 'cm', Expr\Join::WITH, 'cm.conferenceOrganization = co')
            ->leftJoin(User::class, 'u', Expr\Join::WITH, 'cm.user = u');

        $dql->andWhere('co.conference = :conference');
        $parameters['conference'] = $conference;

        /** Check for search string */
        if (isset($data['search'])) {
            $val = $data['search'];
            $dql->andWhere(
                $qb->expr()->orX(
                    "lower(o.name) LIKE lower('%$val%')",
                    "o.inn LIKE lower('%$val%')",
                    "lower(u.lastName) LIKE lower('%$val%')",
                    "lower(u.firstName) LIKE lower('%$val%')",
                    "lower(u.middleName) LIKE lower('%$val%')"
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
            ->addGroupBy('co.id')
            ->setParameters($parameters);

        $queryC = clone $query;

        $query = $query->setMaxResults($limit)->setFirstResult($offset);

        $result = [
            $query->getQuery()->getResult(),
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