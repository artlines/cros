<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 21.01.19
 * Time: 11:05
 */

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Repository\ConferenceOrganizationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ConferenceOrganizationController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__conference_organization__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ConferenceOrganizationController extends ApiController
{
    /**
     * @Route("conference_organization", methods={"GET"}, name="get_all")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var ConferenceOrganizationRepository $confOrgRepo */
        $confOrgRepo = $this->em->getRepository(ConferenceOrganization::class);
        $placeRepository = $this->em->getRepository(Place::class);

        $year = date('Y');

        /** @var Conference|null $conference */
        $conference = $this->em->getRepository(Conference::class)->findOneBy(['year' => $year]);
        if (!$conference) {
            return $this->notFound("Conference with year $year not found.");
        }

        /** @var ConferenceOrganization[] $conferenceOrganizations */
        list($conferenceOrganizations, $totalCount) = $confOrgRepo->searchBy($conference, $this->requestData);

        $items = [];
        foreach ($conferenceOrganizations as $co) {
            $org = $co->getOrganization();
            $members = $co->getConferenceMembers();

            $inRoom = 0;
            foreach ($members as $member) {
                /** @var Place $place */
                if ($placeRepository->findOneBy(['conferenceMember' => $member])) {
                    $inRoom++;
                }
            }

            $items[] = [
                'id'                => $co->getId(),
                'name'              => $org->getName(),
                'inn'               => $org->getInn(),
                'kpp'               => $org->getKpp(),
                'city'              => $org->getCity(),
                'requisites'        => $org->getRequisites(),
                'address'           => $org->getAddress(),
                'total_members'     => $members->count(),
                'in_room_members'   => $inRoom,
                'comments_count'    => $co->getComments()->count(),
                'invoices_count'    => $co->getInvoices()->count(),
            ];
        }

        return $this->success(['items' => $items, 'total_count' => $totalCount]);
    }

    /**
     * @Route("conference_organization/new", methods={"POST"}, name="new")
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

        if (!$name || !$inn) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var Organization $existOrg */
        if ($existOrg = $this->em->getRepository(Organization::class)->findOneBy(['inn' => $inn, 'kpp' => $kpp])) {
            return $this->badRequest("С такими ИНН и КПП есть организация \"{$existOrg->getName()}\"");
        }

        /** @var Conference $conference */
        $conference = $this->em->getRepository(Conference::class)
            ->findOneBy(['year' => $year]);
        if (!$conference) {
            return $this->notFound('Не найдена конференция текущего года.');
        }

        $organization = new Organization();
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
     *
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id)
    {
        $name = $this->requestData['name'] ?? null;
        $inn = $this->requestData['inn'] ?? null;
        $kpp = $this->requestData['kpp'] ?? null;
        $city = $this->requestData['city'] ?? null;
        $address = $this->requestData['address'] ?? null;
        $requisites = $this->requestData['requisites'] ?? null;

        if (!$name || !$inn) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $id)) {
            return $this->notFound('Conference organization not found.');
        }

        $organization = $conferenceOrganization->getOrganization();

        $organization->setName($name);
        $organization->setInn($inn);
        $organization->setKpp($kpp);
        $organization->setCity($city);
        $organization->setAddress($address);
        $organization->setRequisites($requisites);

        $this->em->persist($organization);
        $this->em->flush();

        return $this->success();
    }
}