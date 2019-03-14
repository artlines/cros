<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use App\Entity\Participating\Organization;
use App\Repository\ConferenceOrganizationRepository;
use App\Repository\InvoiceRepository;
use App\Service\Mailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ConferenceOrganizationController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__conference_organization__")
 */
class ConferenceOrganizationController extends ApiController
{
    /**
     * @Route("conference_organization", methods={"GET"}, name="get_all")
     * @IsGranted("ROLE_CMS_USER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var ConferenceOrganizationRepository $confOrgRepo */
        $confOrgRepo = $this->em->getRepository(ConferenceOrganization::class);
        /** @var InvoiceRepository $invoiceRepo */
        $invoiceRepo = $this->em->getRepository(Invoice::class);

        /** @var ConferenceOrganization[] $conferenceOrganizations */
        list($items, $totalCount) = $confOrgRepo->searchByNative($this->requestData);

        $invoices = $invoiceRepo->getInvoicesGroupByConfOrganization();

        foreach ($items as $index => $item) {
            $items[$index]['invoices'] = $invoices[$item['id']] ?? [];
        }

        return $this->success(['items' => $items, 'total_count' => $totalCount]);
    }

    /**
     * @Route("conference_organization/new", methods={"POST"}, name="new")
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function new()
    {
        $year = date('Y');

        $name = $this->requestData['name'] ?? null;
        $inn = $this->requestData['inn'] ?? null;
        $kpp = $this->requestData['kpp'] ?? null;
        $city = $this->requestData['city'] ?? null;
        $address = $this->requestData['address'] ?? null;
        $requisites = $this->requestData['requisites'] ?? null;

        if (!$name || !$inn || !$kpp) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var Conference $conference */
        $conference = $this->em->getRepository(Conference::class)
            ->findOneBy(['year' => $year]);
        if (!$conference) {
            return $this->notFound('Не найдена конференция текущего года.');
        }

        /** @var Organization $organization */
        if (!$organization = $this->em->getRepository(Organization::class)->findOneBy(['inn' => $inn, 'kpp' => $kpp])) {
            $organization = new Organization();
        }

        $organization->setName($name);
        $organization->setInn($inn);
        $organization->setKpp($kpp);
        $organization->setCity($city);
        $organization->setAddress($address);
        $organization->setRequisites($requisites);

        $this->em->persist($organization);

        $conferenceOrganization = new ConferenceOrganization();
        $conferenceOrganization->setOrganization($organization);
        $conferenceOrganization->setConference($conference);

        $this->em->persist($conferenceOrganization);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_organization/{id}", requirements={"id":"\d+"}, methods={"PUT"}, name="update")
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id)
    {
        $name = $this->requestData['name'] ?? null;
        $hidden = $this->requestData['hidden'] ?? false;
        $inn = $this->requestData['inn'] ?? null;
        $kpp = $this->requestData['kpp'] ?? null;
        $city = $this->requestData['city'] ?? null;
        $address = $this->requestData['address'] ?? null;
        $requisites = $this->requestData['requisites'] ?? null;

        if (!$name || !$inn || !$kpp) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $id)) {
            return $this->notFound('Conference organization not found.');
        }

        $organization = $conferenceOrganization->getOrganization();

        /** @var Conference $conference */
        $conference = $this->em->getRepository(Conference::class)
            ->findOneBy(['year' => date('Y')]);
        if (!$conference) {
            $year = date('Y');
            return $this->badRequest("Не найдена конференция для {$year}(текущего) года.");
        }
        /** @var Organization $existOrg */
        $existOrg = $this->em

            ->getRepository(Organization::class)
            ->findOrganizationInConference($conference, $inn, $kpp);

        if ($existOrg && $organization !== $existOrg) {
            return $this->badRequest("С такими ИНН и КПП есть организация \"{$existOrg->getName()}\"");
        }

        $organization->setName($name);
        $organization->setHidden($hidden);
        $organization->setInn($inn);
        $organization->setKpp($kpp);
        $organization->setCity($city);
        $organization->setAddress($address);
        $organization->setRequisites($requisites);

        $this->em->persist($organization);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_organization/{id}", requirements={"id":"\d+"}, methods={"DELETE"}, name="delete")
     * @IsGranted("ROLE_SETTLEMENT_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var ConferenceOrganization $conferenceOrganization */
        $conferenceOrganization = $this->em->find(ConferenceOrganization::class, $id);
        if (!$conferenceOrganization) {
            return $this->notFound('Conference organization not found.');
        }

        /** Check for invoices and members */
        if ($conferenceOrganization->getInvoices()->count()) {
            return $this->badRequest('У организации есть прикрепленные счета. Удалите их прежде чем удалить организацию.');
        }
        if ($conferenceOrganization->getConferenceMembers()->count()) {
            return $this->badRequest('У организации есть прикрепленные участники. Удалите их прежде чем удалять организацию.');
        }

        $this->em->remove($conferenceOrganization);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_organization/invite", methods={"POST"}, name="invite")
     * @IsGranted("ROLE_SALES_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Mailer $mailer
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $parameterBag
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function invite(Mailer $mailer, LoggerInterface $logger, ParameterBagInterface $parameterBag)
    {
        $bcc_emails = $parameterBag->has('invite_bcc_emails') ? $parameterBag->get('invite_bcc_emails') : null;

        $year = date('Y');

        $fio = $this->requestData['fio'] ?? null;
        $email = $this->requestData['email'] ?? null;
        $name = $this->requestData['name'] ?? null;
        $inn = $this->requestData['inn'] ?? null;
        $kpp = (isset($this->requestData['kpp']) && $this->requestData['kpp'] !== '') ? $this->requestData['kpp'] : null;
        $mngr_first_name = $this->requestData['mngr_first_name'] ?? null;
        $mngr_last_name = $this->requestData['mngr_last_name'] ?? null;
        $mngr_phone = $this->requestData['mngr_phone'] ?? null;
        $mngr_email = $this->requestData['mngr_email'] ?? null;

        $logger->notice('[Invite Organization] Received data', ['data' => $this->requestData]);

        if (!$fio || !$email || !$name || !$inn || is_null($kpp) || !$mngr_first_name || !$mngr_last_name || !$mngr_phone || !$mngr_email) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var Conference $conference */
        $conference = $this->em->getRepository(Conference::class)
            ->findOneBy(['year' => $year]);
        if (!$conference) {
            return $this->notFound("Conference with year $year not found.");
        }

        $logger->notice('[Invite Organization] Try to found organization', ['inn' => $inn, 'kpp' => $kpp]);

        /** @var Organization $organization */
        if ($organization = $this->em->getRepository(Organization::class)->findOneBy(['inn' => $inn, 'kpp' => $kpp])) {
            $logger->notice('[Invite Organization] Found Organization', [
                'id'    => $organization->getId(),
                'name'  => $organization->getName()
            ]);

            /**
             * Check that organization already invited or registered
             * @var ConferenceOrganization $conferenceOrganization
             */
            $conferenceOrganization = $this->em->getRepository(ConferenceOrganization::class)
                ->findOneBy(['conference' => $conference, 'organization' => $organization]);

            if ($conferenceOrganization) {
                $logger->notice('[Invite Organization] Found ConferenceOrganization', [
                    'id'        => $conferenceOrganization->getId(),
                    'is_finish' => $conferenceOrganization->isFinish() ? 'true' : 'false'
                ]);

                if ($conferenceOrganization->isFinish()) {
                    return $this->badRequest('Организация уже зарегистрирована.');
                } elseif ($employee = $conferenceOrganization->getInvitedBy()) {
                    return $this->badRequest('Организация уже приглашена. Приглашение отослал(а) '.$employee->getFullName());
                } else {
                    return $this->badRequest('Организация уже участвует.');
                }
            }
        } else {
            $logger->notice('[Invite Organization] Not found Organization, create new');

            $organization = new Organization();
            $organization->setInn($inn);
            $organization->setKpp($kpp);
            $organization->setName($name);
        }

        $inviteHash = sha1(random_bytes(10));
        $inviteData = [
            'mngr_first_name'   => $mngr_first_name,
            'mngr_last_name'    => $mngr_last_name,
            'mngr_phone'        => $mngr_phone,
            'mngr_email'        => $mngr_email,
            'fio'               => $fio,
        ];

        $organization->setEmail($email);
        $this->em->persist($organization);

        $conferenceOrganization = new ConferenceOrganization();
        $conferenceOrganization->setConference($conference);
        $conferenceOrganization->setOrganization($organization);
        $conferenceOrganization->setInvitedBy($this->getUser());
        $conferenceOrganization->setInviteHash($inviteHash);
        $conferenceOrganization->setInviteData($inviteData);
        $this->em->persist($conferenceOrganization);
        $this->em->flush();

        $data = $inviteData;
        $data['hash'] = $inviteHash;
        $data['org_name'] = $name;
        $mailer->send('Приглашаем вас на КРОС-2019', $data, $email, null, array_merge($bcc_emails, [$data['mngr_email']]));
        $logger->notice('[Invite Organization] Organization invited', ['data' => $data]);

        return $this->success();
    }

    /**
     * @Route("conference_organization/re_invite/{id}", requirements={"id":"\d+"}, methods={"GET"}, name="re_invite")
     * @IsGranted("ROLE_SALES_MANAGER")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @param Mailer $mailer
     * @param ParameterBagInterface $parameterBag
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function reInvite($id, Mailer $mailer, ParameterBagInterface $parameterBag, LoggerInterface $logger)
    {
        $bcc_emails = $parameterBag->has('invite_bcc_emails') ? $parameterBag->get('invite_bcc_emails') : null;

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $id)) {
            return $this->notFound('Conference organization not found.');
        }

        if (!$conferenceOrganization->getInvitedBy() || !$conferenceOrganization->getInviteHash()) {
            return $this->badRequest('Conference organization not contains invited_by or invite_hash param.');
        }

        $organization = $conferenceOrganization->getOrganization();

        if (!$email = $organization->getEmail()) {
            return $this->badRequest('У организации не указан email.');
        }

        $data = $conferenceOrganization->getInviteData();
        if (!$data) {
            return $this->badRequest('Параметры для приглашения не заполнены.');
        }
        $data['hash'] = $conferenceOrganization->getInviteHash();
        $data['org_name'] = $organization->getName();
        $mailer->send('Приглашаем вас на КРОС-2019', $data, $email, null, array_merge($bcc_emails, [$data['mngr_email']]));
        $logger->notice('[Invite Organization] Organization reinvited', ['id' => $id, 'data' => $data]);

        return $this->success();
    }
}