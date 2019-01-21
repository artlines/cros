<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Participating\ConferenceOrganization;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ConferenceMemberController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__conference_member__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ConferenceMemberController extends ApiController
{

    /**
     * @Route("conference_member", methods={"GET"}, name="get_all")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
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
        foreach ($conferenceOrganization->getConferenceMembers() as $conferenceMember) {
            $member = $conferenceMember->getUser();

            $placeInfo = ['room_num' => null, 'approved' => null];
            /** @var Place $place */
            $place = $this->em->getRepository(Place::class)
                ->findOneBy(['conferenceMember' => $conferenceMember]);
            if ($place) {
                $placeInfo['room_num'] = $place->getRoom()->getApartment()->getNumber();
                $placeInfo['approved'] = $place->isApproved() ? 'true' : 'false';
            }


            $items[] = [
                'id'            => $member->getId(),
                'first_name'    => $member->getFirstName(),
                'last_name'     => $member->getLastName(),
                'middle_name'   => $member->getMiddleName(),
                'post'          => $member->getPost(),
                'phone'         => $member->getPhone(),
                'email'         => $member->getEmail(),
                'place'         => $placeInfo,
            ];
        }

        return $this->success(['items' => $items]);
    }
}