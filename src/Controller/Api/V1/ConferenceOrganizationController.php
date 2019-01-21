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
                'total_members'     => $members->count(),
                'in_room_members'   => $inRoom,
                'comments_count'    => $co->getComments()->count(),
                'invoices_count'    => $co->getInvoices()->count(),
            ];
        }

        return $this->success(['items' => $items, 'total_count' => $totalCount]);
    }
}