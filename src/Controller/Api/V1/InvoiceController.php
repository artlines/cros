<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class InvoiceController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__invoice__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class InvoiceController extends ApiController
{
    /**
     * @Route("invoice", methods={"GET"}, name="get_by_org")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        $conference_organization_id = $this->requestData['conference_organization_id'] ?? null;

        if (!$conference_organization_id) {
            return $this->badRequest('conference_organization_id not set');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        $conferenceOrganization = $this->em->find(ConferenceOrganization::class, $conference_organization_id);
        if (!$conferenceOrganization) {
            return $this->notFound('Conference Organization not found.');
        }

        $items = [];
        foreach ($conferenceOrganization->getInvoices() as $invoice) {
            $items[] = [
                'id'        => $invoice->getId(),
                'number'    => $invoice->getNumber(),
                'amount'    => $invoice->getAmount(),
                'date'      => $invoice->getDate()->getTimestamp(),
                'status'    => $invoice->getStatus(),
            ];
        }

        return $this->success(['items' => $items]);
    }
}