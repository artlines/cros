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
            WITH tmp_representative_members AS (
              SELECT po.id as org_id
              FROM participating.organization po
                LEFT JOIN participating.member pm ON po.id = pm.organization_id
              WHERE pm.representative = TRUE
            ),
            tmp_places AS (
              SELECT po.id as org_id, pco.id as conf_org_id, COUNT(ap.id) as place_count
              FROM abode.place ap
                LEFT JOIN participating.conference_member       pcm ON ap.conference_member_id = pcm.id
                LEFT JOIN participating.conference_organization pco ON pcm.conference_organization_id = pco.id
                LEFT JOIN participating.organization            po ON pco.organization_id = po.id
              GROUP BY po.id, pco.id
            ),
            tmp_members AS (
              SELECT po.id AS org_id, po.name AS org_name, pco.id AS conf_org_id, COUNT(pcm.id) AS member_count, SUM(art.cost) AS summa
              FROM participating.organization po
                INNER JOIN participating.conference_organization pco ON po.id = pco.organization_id
                INNER JOIN public.conference                      pc ON pco.conference_id = pc.id AND pc.year = :year
                INNER JOIN participating.conference_member       pcm ON pco.id = pcm.conference_organization_id
                INNER JOIN abode.room_type                       art ON pcm.room_type_id = art.id
              WHERE po.id IN ( SELECT org_id FROM tmp_representative_members )
              GROUP BY po.id, po.name, pco.id
            ),
            tmp_invoice_pre AS (
              SELECT pi.id, pi.conference_organization_id AS conf_org_id, pi.amount, pi.b2b_order_guid,
                RANK() OVER (PARTITION BY pi.conference_organization_id ORDER BY pi.created_at DESC) as invoice_rnk
              FROM participating.invoice pi
              WHERE pi.conference_organization_id IN ( SELECT conf_org_id FROM tmp_members )
            ),
            tmp_invoice AS (
              SELECT *
              FROM tmp_invoice_pre
              WHERE invoice_rnk = 1
            )
            SELECT t_m.org_id, t_m.org_name, t_m.conf_org_id, t_i.id as invoice_id, t_i.amount as last_invoice_amount, t_m.summa as fresh_amount
            FROM tmp_members t_m
              LEFT JOIN tmp_invoice t_i ON t_m.conf_org_id = t_i.conf_org_id
              INNER JOIN tmp_places  t_p ON t_p.conf_org_id = t_m.conf_org_id AND t_p.place_count = t_m.member_count
            WHERE
                  t_i.amount IS NULL OR t_i.amount != t_m.summa
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