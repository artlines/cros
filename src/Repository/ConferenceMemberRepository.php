<?php

namespace App\Repository;

use App\Entity\Participating\Invoice;
use App\Entity\Participating\User;
use Doctrine\ORM\EntityRepository;

class ConferenceMemberRepository extends EntityRepository
{
    public function getNotSettled($year, $housing_id = null)
    {
        $conn = $this->getEntityManager()->getConnection();

        $parameters = [
            'year'                      => $year,
            'status_guid_fully_payed'   => Invoice::STATUS_GUID__FULLY_PAYED,
            'status_fully_payed'        => Invoice::STATUS__FULLY_PAYED
        ];

        $query = "
            WITH tmp_housing_room_types AS (
              SELECT
                     array_agg(DISTINCT ah.id) as housing_id,
                     art.id as room_type_id
              FROM abode.housing ah
                LEFT JOIN abode.apartment aa ON ah.id = aa.housing_id
                LEFT JOIN abode.room ar ON aa.id = ar.apartment_id
                INNER JOIN abode.room_type art ON ar.type_id = art.id
              GROUP BY art.id
            ),
            tmp_neighbourhoods AS (
              SELECT
                     pcm.id as conf_member_id,
                     pm.last_name || ' ' || pm.first_name AS neighbourhood
              FROM participating.conference_member pcm
                LEFT JOIN participating.conference_member pcm2 ON pcm2.id = pcm.neighbourhood_id
                LEFT JOIN participating.member pm ON pcm2.user_id = pm.id
            ),
            tmp_managers AS (
              SELECT
                     pcm.id as conf_member_id,
                     pm.first_name || ' ' || pm.last_name as manager_name
              FROM participating.conference_member pcm
                LEFT JOIN participating.conference_organization pco ON pcm.conference_organization_id = pco.id
                LEFT JOIN participating.member pm ON pco.invited_by = pm.id
            ),
            tmp_conf_org_info AS (
              SELECT
                     pco.id as conf_org_id,
                     po.name as org_name,
                     COUNT(pi.id) as invoices_count,
            COUNT(pi.id) FILTER (WHERE pi.status_guid = :status_guid_fully_payed OR pi.status_id = :status_fully_payed) as invoices_payed
              FROM participating.conference_organization pco
                LEFT JOIN participating.invoice pi ON pco.id = pi.conference_organization_id
                LEFT JOIN participating.organization po ON pco.organization_id = po.id
              GROUP BY pco.id, po.name
            ),
            tmp_not_settled AS (
              SELECT
                     pcm.id as conf_member_id
              FROM participating.conference_member pcm
                LEFT JOIN abode.place ap ON pcm.id = ap.conference_member_id
              WHERE ap.id IS NOT NULL
            )
            SELECT
                   pcm.id,
                   pm.first_name,
                   pm.last_name,
                   tcoi.org_name,
                   tcoi.invoices_count,
                   tcoi.invoices_payed,
                   pcm.room_type_id,
                   tn.neighbourhood,
                   tm.manager_name,
                   thrt.housing_id
            FROM participating.conference_member pcm
              INNER JOIN public.conference          pc ON pcm.conference_id = pc.id AND pc.year = :year
              LEFT JOIN abode.place                 ap ON pcm.id = ap.conference_member_id
              LEFT JOIN participating.member        pm ON pcm.user_id = pm.id
              LEFT JOIN tmp_conf_org_info         tcoi ON pcm.conference_organization_id = tcoi.conf_org_id
              LEFT JOIN tmp_neighbourhoods          tn ON pcm.id = tn.conf_member_id
              LEFT JOIN tmp_managers                tm ON pcm.id = tm.conf_member_id
              LEFT JOIN tmp_housing_room_types    thrt ON pcm.room_type_id = thrt.room_type_id
            WHERE ap.id IS NULL
        ";

        if ($housing_id > 0) {
            $query .= " AND :housing_id = ANY (thrt.housing_id)";
            $parameters['housing_id'] = $housing_id;
        }

        $query .= " ORDER BY pcm.id DESC";

        $stmt = $conn->prepare($query);

        $stmt->execute($parameters);

        return $stmt->fetchAll();
    }

    /**
     * @param $conference_id
     * @param $email
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findConferenceMemberByEmail($conference_id, $email)
    {
        return $this
            ->createQueryBuilder('cm')
            ->leftJoin(User::class, 'u',"WITH",'cm.user=u.id')
            ->where('cm.conference = :conference_id')
            ->andWhere('u.email = :email')
            ->setParameters([
                'conference_id' => $conference_id,
                'email' => $email,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}