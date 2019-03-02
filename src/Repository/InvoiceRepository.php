<?php

namespace App\Repository;

use App\Entity\Participating\Invoice;
use Doctrine\ORM\EntityRepository;

class InvoiceRepository extends EntityRepository
{
    /**
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