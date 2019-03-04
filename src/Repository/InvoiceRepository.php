<?php

namespace App\Repository;

use App\Entity\Participating\Invoice;
use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
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
            ->where($qb->expr()->isNotNull('i.orderGuid'));

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