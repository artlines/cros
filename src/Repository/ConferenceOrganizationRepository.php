<?php

namespace App\Repository;

use App\Entity\Abode\Place;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ConferenceOrganizationRepository extends EntityRepository
{
    const STAGE__INVITE_SENT                = 1;
    const STAGE__REGISTRATION_COMPLETE      = 2;
    const STAGE__MEMBERS_SETTLED            = 3;
    const STAGE__INVOICE_MADE_AND_NOT_PAYED = 4;
    const STAGE__INVOICE_PAYED              = 5;
    const STAGE__INVOICE_CANCELED           = 6;

    public function findToMakeInvoice(int $year)
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            WITH tmp_representative_members_pre AS (
              SELECT
                po.id as org_id,
                pm.email as user_email,
                pm.phone as user_phone,
                pm.b2b_guid as user_guid,
                RANK() OVER (PARTITION BY po.id ORDER BY pm.b2b_guid ASC) as user_rnk
              FROM participating.organization po
                INNER JOIN participating.member pm ON po.id = pm.organization_id AND pm.representative = TRUE
            ),
            tmp_representative_members AS (
              SELECT *
              FROM tmp_representative_members_pre
              WHERE user_rnk = 1
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
              SELECT po.id AS org_id, po.b2b_guid AS org_b2b_guid, po.name AS org_name, pco.id AS conf_org_id, COUNT(pcm.id) AS member_count, SUM(art.cost) AS summa
              FROM participating.organization po
                INNER JOIN participating.conference_organization pco ON po.id = pco.organization_id
                INNER JOIN public.conference                      pc ON pco.conference_id = pc.id AND pc.year = :year
                INNER JOIN participating.conference_member       pcm ON pco.id = pcm.conference_organization_id
                INNER JOIN abode.room_type                       art ON pcm.room_type_id = art.id
              WHERE po.id IN ( SELECT org_id FROM tmp_representative_members ) AND po.b2b_guid IS NOT NULL AND po.b2b_guid IS NOT NULL AND (po.invalid_inn_kpp = FALSE OR po.invalid_inn_kpp IS NULL)
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
            SELECT
                   t_m.org_id,
                   t_m.org_name,
                   t_m.org_b2b_guid,
                   t_m.conf_org_id,
                   t_rm.user_guid,
                   t_rm.user_email,
                   t_rm.user_phone,
                   t_i.id as invoice_id,
                   t_i.b2b_order_guid as invoice_b2b_order_guid,
                   t_i.amount as last_invoice_amount,
                   t_m.summa as fresh_amount
            FROM tmp_members t_m
              INNER JOIN tmp_invoice                 t_i ON t_m.conf_org_id = t_i.conf_org_id
              INNER JOIN tmp_places                 t_p ON t_p.conf_org_id = t_m.conf_org_id AND t_p.place_count = t_m.member_count
              LEFT JOIN tmp_representative_members t_rm ON t_rm.org_id = t_m.org_id
            WHERE TRUE AND t_i.b2b_order_guid IS NULL
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
            ->select('co, cm, u')
            ->leftJoin(Organization::class, 'o', Expr\Join::WITH, 'co.organization = o')
            ->leftJoin(Conference::class, 'c', Expr\Join::WITH, 'co.conference = c')
            ->leftJoin('co.conferenceMembers', 'cm')
            ->leftJoin('cm.user', 'u')
            ->where('c.year = :year')
            ->andWhere('u.representative = true')
            ->andWhere('u.b2b_guid is not null')
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
     */
    public function searchByNative(array $data = [])
    {
        $conn = $this->getEntityManager()->getConnection();

        $year = (int) date('Y');
        $limit = 1000;
        $offset = 0;
        $where = 'TRUE';

        $parameters = [
            'year'                              => $year,
            'invoice_fully_payed_status_id'     => Invoice::STATUS__FULLY_PAYED,
            'invoice_fully_payed_status_guid'   => Invoice::STATUS_GUID__FULLY_PAYED,
        ];

        /**
         * Check filter
         * @deprecated because add staging
         */
        if (isset($data['no_auto_invoicing']) && $data['no_auto_invoicing']) {
            $where .= " AND (
                tcoi.invalid_inn_kpp IS NULL 
                  OR tcoi.invalid_inn_kpp = TRUE
                  OR tms.representative_members = 0
                  OR tms.total_members != tms.in_room_members
                )";
        }
        // проверяем выбор для "не определено"
        // Исключаем его, если указано.
        // Если конструкция более сложная, тогда исключаем её из массива данных

        $closeOR = false;

        if (isset($data['invited_by'])) {
            if (false !== $key = array_search(-1, $data['invited_by'])) {
                unset($data['invited_by'][$key]);
                if( 0 == count($data['invited_by']) ){
                    unset($data['invited_by']);
                    $where .= " AND tcoi.invited_by_id IS NULL";
                } else {
                    $where .= " AND ( tcoi.invited_by_id IS NULL OR TRUE ";
                    $closeOR = ")";
                }
            }
        }
        /** Check invited_by filter */
        if (isset($data['invited_by'])) {
            $_invited_by = is_array($data['invited_by']) ? $data['invited_by'] : [$data['invited_by']];
            $where .= " AND tcoi.invited_by_id IN (".implode($_invited_by, ', ').")";
        }

        // Закрывавающая конструкция, если определена
        if ($closeOR){
            $where .= $closeOR;
        }

        /** Check search string */
        if (isset($data['search'])) {
            $where .= " AND tsi.search ILIKE :search";
            $parameters['search'] = '%'.mb_strtolower($data['search']).'%';
        }

        /** Check flag that show only with comments */
        if (isset($data['with_comments'])) {
            $where .= " AND tcs.comments_count != 0";
        }

        /** Check flag that show only with comments */
        if (isset($data['without_manager'])) {
            $where .= " AND pco.invited_by IS NULL";
        }

        /** Check stage filter */
        if (isset($data['stage'])) {
            switch ((int) $data['stage']) {
                case self::STAGE__INVITE_SENT:
                    $where .= " AND tms.total_members = 0";
                    break;
                case self::STAGE__REGISTRATION_COMPLETE:
                    $where .= " AND tms.total_members > 0 AND tms.total_members > tms.in_room_members";
                    break;
                case self::STAGE__MEMBERS_SETTLED:
                    $where .= " AND tms.total_members > 0 AND tms.total_members = tms.in_room_members AND tis.invoices_count = 0";
                    break;
                case self::STAGE__INVOICE_MADE_AND_NOT_PAYED:
                    $where .= " AND tis.invoices_count != tis.invoices_payed AND tis.invoices_count > 0";
                    break;
                case self::STAGE__INVOICE_PAYED:
                    $where .= " AND tlii.status_guid = :invoice_fully_payed_status_guid__stage";
                    $parameters['invoice_fully_payed_status_guid__stage'] = Invoice::STATUS_GUID__FULLY_PAYED;
                    break;
                case self::STAGE__INVOICE_CANCELED:
                    $where .= " AND tlii.order_status_guid = :order_canceled_status_guid__stage";
                    $parameters['order_canceled_status_guid__stage'] = Invoice::ORDER_STATUS_GUID__CANCELED;
                    break;
                default:
                    // nothing
                    break;
            }
        }

        /** Check for limit and offset */
        if (isset($data['@limit'])) {
            $limit = (int) $data['@limit'];
        }
        if (isset($data['@offset'])) {
            $offset = (int) $data['@offset'];
        }

        $query = "
            WITH tmp_last_invoice_info_pre AS (
              SELECT
                     pco.id as conf_org_id,
                     pi.id,
                     pi.is_sent,
                     pi.status_guid,
                     pi.order_status_guid,
                     RANK() OVER (PARTITION BY pco.id ORDER BY pi.created_at DESC, pi.id DESC) as invoice_rnk
              FROM participating.conference_organization pco
                LEFT JOIN participating.invoice pi ON pi.conference_organization_id = pco.id
            ),
            tmp_last_invoice_info AS (
              SELECT
                     pco.id as conf_org_id,
                     tliip.id,
                     tliip.is_sent,
                     tliip.status_guid,
                     tliip.order_status_guid
              FROM participating.conference_organization pco
                LEFT JOIN tmp_last_invoice_info_pre tliip ON tliip.conf_org_id = pco.id
              WHERE tliip.invoice_rnk = 1
            ),
            tmp_invoices_stat AS (
              SELECT
                     pco.id AS conf_org_id,
                     COUNT(pi.*) as invoices_count,
                     COUNT(pi.*) FILTER (
                          WHERE pi.status_id = :invoice_fully_payed_status_id
                            OR pi.status_guid = :invoice_fully_payed_status_guid
                       ) as invoices_payed
              FROM participating.conference_organization pco
                LEFT JOIN participating.organization po ON pco.organization_id = po.id
                LEFT JOIN participating.invoice pi ON pco.id = pi.conference_organization_id
              GROUP BY pco.id
            ),
            tmp_representative_members_pre AS (
              SELECT
                     po.id as org_id,
                     pm.email as user_email,
                     pm.phone as user_phone,
                     pm.b2b_guid as user_guid,
                     RANK() OVER (PARTITION BY po.id ORDER BY pm.b2b_guid ASC) as user_rnk
              FROM participating.organization po
                LEFT JOIN participating.member pm ON po.id = pm.organization_id AND pm.representative = TRUE
            ),
            tmp_search_idx AS (
              SELECT
                     pco.id as conf_org_id,
                     CONCAT_WS(' ', po.name, po.inn::text, pm.last_name, pm.first_name, pm.middle_name) as search
              FROM participating.conference_organization pco
                LEFT JOIN participating.conference_member pcm ON pco.id = pcm.conference_organization_id
                LEFT JOIN participating.member pm ON pcm.user_id = pm.id
                LEFT JOIN participating.organization po ON pco.organization_id = po.id
            ),
            tmp_members_stat AS (
              SELECT
                     pco.id AS conf_org_id,
                     COUNT(pcm.*) AS total_members,
                     COUNT(pm.*) FILTER (WHERE pm.representative = TRUE) AS representative_members,
                     COUNT(ap.*) FILTER (WHERE ap.id IS NOT NULL) as in_room_members
              FROM participating.conference_organization pco
                LEFT JOIN participating.conference_member pcm ON pco.id = pcm.conference_organization_id
                LEFT JOIN participating.member pm ON pcm.user_id = pm.id
                LEFT JOIN abode.place ap ON pcm.id = ap.conference_member_id
              GROUP BY pco.id
            ),
            tmp_conf_org_info AS (
              SELECT
                     pco.id as conf_org_id,
                     po.name,
                     po.hidden,
                     po.inn,
                     po.kpp,
                     po.email,
                     po.invalid_inn_kpp,
                     po.city,
                     po.address,
                     po.requisites,
                     pm.id as invited_by_id,
                     CONCAT_WS(' ', pm.last_name, pm.first_name) as invited_by
              FROM participating.conference_organization pco
                LEFT JOIN participating.organization po ON pco.organization_id = po.id
                LEFT JOIN participating.member       pm ON pco.invited_by = pm.id
            ),
            tmp_comments_stat AS (
              SELECT
                     pco.id as conf_org_id,
                     COUNT(pc.*) as comments_count
              FROM participating.conference_organization pco
                LEFT JOIN participating.comment pc ON pco.id = pc.conference_organization_id
              GROUP BY pco.id
            )
            SELECT
                   pco.id,
                   tcoi.name,
                   tcoi.hidden,
                   tcoi.email,
                   tcoi.inn,
                   tcoi.kpp,
                   tcoi.invalid_inn_kpp,
                   tcoi.city,
                   tcoi.address,
                   tcoi.requisites,
                   tcoi.invited_by_id,
                   tcoi.invited_by,
                   tms.total_members,
                   tms.in_room_members,
                   tcs.comments_count,
                   tis.invoices_count,
                   tis.invoices_payed,
                   pco.finish as is_finish
            FROM participating.conference_organization pco
              INNER JOIN public.conference      puc ON pco.conference_id = puc.id AND puc.year = :year
              INNER JOIN tmp_invoices_stat      tis ON pco.id = tis.conf_org_id
              INNER JOIN tmp_conf_org_info     tcoi ON pco.id = tcoi.conf_org_id
              INNER JOIN tmp_members_stat       tms ON pco.id = tms.conf_org_id
              INNER JOIN tmp_search_idx         tsi ON pco.id = tsi.conf_org_id
              INNER JOIN tmp_comments_stat      tcs ON pco.id = tcs.conf_org_id
              INNER JOIN tmp_last_invoice_info tlii ON pco.id = tlii.conf_org_id
            WHERE $where
            GROUP BY
                   pco.id,
                   tcoi.name,
                   tcoi.hidden,
                   tcoi.email,
                   tcoi.inn,
                   tcoi.kpp,
                   tcoi.invalid_inn_kpp,
                   tcoi.city,
                   tcoi.address,
                   tcoi.requisites,
                   tcoi.invited_by_id,
                   tcoi.invited_by,
                   tms.total_members,
                   tms.in_room_members,
                   tcs.comments_count,
                   tis.invoices_count,
                   tis.invoices_payed
        ";

        $queryC = $query;
        $query .= " ORDER BY tcoi.invited_by LIMIT $limit OFFSET $offset";

        $stmt = $conn->prepare($queryC);
        $stmt->execute($parameters);
        $count = count($stmt->fetchAll());

        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);
        $items = $stmt->fetchAll();

        return [$items, $count];
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


    /**
     * Отображать только те организации, которые имеют заселенных участников.
     * Не показывать организации, которые имеют признак hidden.
     *
     * @param Conference $conference
     * @return mixed
     */
    public function findShowByConference(Conference $conference)
    {
        return $this
            ->createQueryBuilder('co')
            ->innerJoin(
                Organization::class,
                'o',
                Expr\Join::WITH,
                'co.organization = o.id AND coalesce(o.hidden,false) = :hidden'
            )
            ->innerJoin(
                ConferenceMember::class,
                'cm',
                Expr\Join::WITH,
                'co.id = cm.conferenceOrganization'
            )
            ->innerJoin(
                Place::class,
                'p',
                Expr\Join::WITH,
                'p.conferenceMember = cm.id'
            )
            ->innerJoin(
                Invoice::class,
                'i',
                Expr\Join::WITH,
                'i.conferenceOrganization = co.id AND i.statusGuid = :status_guid_fully_payed'
            )
            ->where('co.conference = :conference')
            ->setParameters([
                'hidden'                   => 'false',
                'conference'               => $conference,
                'status_guid_fully_payed'  => Invoice::STATUS_GUID__FULLY_PAYED,
            ])
            ->groupBy('co.id, o.id')
            ->orderBy('co.priority','DESC')
            ->orderBy('o.name','ASC')
            ->getQuery()
            ->getResult()
            ;

    }

}