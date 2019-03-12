<?php

namespace App\Repository;

use App\Entity\Participating\Invoice;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    /**
     * Find invoices which didn't make in auto mode
     *
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param \DateInterval $interval Interval which will be sub from `now` DateTime and push to query as `from_created_at`
     * @return array
     */
    public function findFailedAutoInvoicing(\DateInterval $interval)
    {
        $conn = $this->getEntityManager()->getConnection();

        $parameters = [
            'from_created_at'   => (new \DateTime('now'))->sub($interval)->format('Y-m-d H:i:s'),
            'status_guid__wait' => Invoice::STATUS_GUID__DOCUMENT_NOT_READY,
        ];

        $query = "
            SELECT
                   po.name,
                   po.inn,
                   po.kpp,
                   po.invalid_inn_kpp,
                   po.b2b_guid as org_guid,
                   pi.id,
                   pi.num,
                   pi.b2b_order_guid as order_guid,
                   pi.created_at
            FROM participating.invoice pi
              LEFT JOIN participating.conference_organization pco ON pi.conference_organization_id = pco.id
              LEFT JOIN participating.organization             po ON pco.organization_id = po.id
            WHERE
                  pi.status_guid = :status_guid__wait
                  AND pi.created_at < :from_created_at
        ";

        try {
            $stmt = $conn->prepare($query);
        } catch (DBALException $e) {
            return [];
        }

        $stmt->execute($parameters);

        return $stmt->fetchAll();
    }

    public function getInvoicesGroupByConfOrganization()
    {
        $conn = $this->getEntityManager()->getConnection();

        $parameters = [
            'invoice_fully_payed_status_id'     => Invoice::STATUS__FULLY_PAYED,
            'invoice_fully_payed_status_guid'   => Invoice::STATUS_GUID__FULLY_PAYED,
        ];

        $query = "
            SELECT
                   pco.id as conf_org_id,
                   pi.id as id,
                   pi.status_id as status,
                   pi.num as number,
                   pi.amount as amount,
                   CASE WHEN (pi.status_id = :invoice_fully_payed_status_id OR pi.status_guid = :invoice_fully_payed_status_guid) 
                       THEN TRUE 
                       ELSE FALSE 
                   END as payed
            FROM participating.conference_organization pco
              INNER JOIN participating.invoice pi ON pco.id = pi.conference_organization_id
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute($parameters);

        $items = $stmt->fetchAll();

        $result = [];
        foreach ($items as $item) {
            if (!isset($result[$item['conf_org_id']])) {
                $result[$item['conf_org_id']] = [];
            }

            $result[$item['conf_org_id']][] = $item;
        }

        return $result;
    }

    /**
     * Get invoices which need synchronize with b2b
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return Invoice[]
     */
    public function getWithOrderGuidToSync()
    {
        $qb = $this->createQueryBuilder('i');

        $query = $qb
            ->select('i')
            ->where($qb->expr()->isNotNull('i.orderGuid'))
            ->andWhere('i.orderStatusGuid != :order_status_guid__canceled')
            ->andWhere('i.statusGuid != :status_guid__fully_payed')
            ->setParameters([
                'status_guid__fully_payed'      => Invoice::STATUS_GUID__FULLY_PAYED,
                'order_status_guid__canceled'   => Invoice::ORDER_STATUS_GUID__CANCELED,
            ]);

        return $query->getQuery()->getResult();
    }

    /**
     * Get information about invoices which need send to the representative members of organization
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getInfoToSend()
    {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("
            SELECT
                   pi.id,
                   po.name as org_name,
                   pi.b2b_order_guid,
                   json_agg(pm.email) as emails
            FROM participating.invoice pi
              LEFT JOIN participating.conference_organization pco ON pi.conference_organization_id = pco.id
              LEFT JOIN participating.organization po ON pco.organization_id = po.id
              LEFT JOIN participating.conference_member pcm ON pco.id = pcm.conference_organization_id
              INNER JOIN participating.member pm ON pcm.user_id = pm.id AND pm.representative = TRUE
            WHERE pi.status_guid != :status_doc_not_ready AND pi.is_sent = FALSE
            GROUP BY pi.id, po.name
        ");

        $stmt->execute([
            'status_doc_not_ready' => Invoice::STATUS_GUID__DOCUMENT_NOT_READY,
        ]);

        return $stmt->fetchAll();
    }
}