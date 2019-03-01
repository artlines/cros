<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use App\Service\B2BApi;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
                'id'            => $invoice->getId(),
                'number'        => $invoice->getNumber(),
                'amount'        => $invoice->getAmount(),
                'date'          => $invoice->getDate()->format("Y-m-d"),
                'status'        => $invoice->getStatus(),
                'status_text'   => $invoice->getStatusText(),
                'doc_ready'     => $invoice->isDocumentReady(),
            ];
        }

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("invoice/new", methods={"POST"}, name="new")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function new()
    {
        $amount = $this->requestData['amount'] ?? null;
        $number = $this->requestData['number'] ?? null;
        $date = $this->requestData['date'] ?? null;
        $status = $this->requestData['status'] ?? null;
        $conference_organization_id = $this->requestData['conference_organization_id'] ?? null;

        if (!$amount || !$number || !$date || !$status || !$conference_organization_id) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $conference_organization_id)) {
            return $this->notFound('Conference organization not found.');
        }

        $invoice = new Invoice();
        $invoice->setAmount($amount);
        $invoice->setConferenceOrganization($conferenceOrganization);
        $invoice->setNumber($number);
        $invoice->setStatus($status);
        $invoice->setDate(new \DateTime($date));

        $this->em->persist($invoice);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("invoice/{id}", requirements={"id":"\d+"}, methods={"PUT"}, name="update")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function update($id)
    {
        $amount = $this->requestData['amount'] ?? null;
        $number = $this->requestData['number'] ?? null;
        $date = $this->requestData['date'] ?? null;
        $status = $this->requestData['status'] ?? null;

        if (!$amount || !$number || !$date || !$status) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var Invoice $invoice */
        if (!$invoice = $this->em->find(Invoice::class, $id)) {
            return $this->notFound('Invoice not found.');
        }

        $invoice->setAmount($amount);
        $invoice->setNumber($number);
        $invoice->setStatus($status);
        $invoice->setDate(new \DateTime($date));

        $this->em->persist($invoice);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("invoice/{id}", requirements={"id":"\d+"}, methods={"DELETE"}, name="delete")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var Invoice $invoice */
        if (!$invoice = $this->em->find(Invoice::class, $id)) {
            return $this->notFound('Invoice not found.');
        }

        $this->em->remove($invoice);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("invoice/{id}/download", requirements={"id":"\d+"}, methods={"GET"}, name="get_document")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @param B2BApi $b2BApi
     * @return \Symfony\Component\HttpFoundation\JsonResponse|StreamedResponse
     */
    public function download($id, B2BApi $b2BApi)
    {
        /** @var Invoice $invoice */
        if (!$invoice = $this->em->find(Invoice::class, $id)) {
            return $this->notFound('Invoice not found.');
        }

        $docName = $invoice->getDocumentName();

        return new StreamedResponse(function () use ($b2BApi, $invoice) {
            $b2BApi->getOrderInvoiceFile($invoice->getOrderGuid());
        }, 200, [
            'Content-Type'          => 'application/pdf',
            'Content-Disposition'   => 'inline; filename="'.$docName.'"',
        ]);
    }
}