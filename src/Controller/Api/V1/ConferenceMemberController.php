<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Abode\RoomType;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Invoice;
use App\Entity\Participating\User;
use App\Repository\ConferenceMemberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $year = date('Y');

        /** @var ConferenceMemberRepository $conferenceMemberRepo */
        $conferenceMemberRepo = $this->em->getRepository(ConferenceMember::class);

        $items = $conferenceMemberRepo->getMembersInfo($year, $this->requestData);

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("conference_member/new", methods={"POST"}, name="new")
     *
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function new(UserPasswordEncoderInterface $encoder)
    {
        $conference_organization_id = $this->requestData['conference_organization_id'] ?? null;
        $first_name = $this->requestData['first_name'] ?? null;
        $last_name = $this->requestData['last_name'] ?? null;
        $middle_name = $this->requestData['middle_name'] ?? null;
        $post = $this->requestData['post'] ?? null;
        $phone = isset($this->requestData['phone']) ? preg_replace('/[^0-9]/', '', $this->requestData['phone']) : null;
        $email = $this->requestData['email'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $car_number = $this->requestData['car_number'] ?? null;
        $arrival = $this->requestData['arrival'] ?? null;
        $leaving = $this->requestData['leaving'] ?? null;
        $representative = isset($this->requestData['representative']) ? (bool) $this->requestData['representative'] : false;
        $room_type_id = $this->requestData['room_type_id'] ?? null;

        if (!$conference_organization_id || !$first_name || !$last_name || !$phone || !$email || !$room_type_id) {
            return $this->badRequest('Не переданы обязательные параметры.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $conference_organization_id)) {
            return $this->notFound('Conference organization not found.');
        }

        /** @var User $member */
        if (!$member = $this->em->getRepository(User::class)->findOneBy(['email' => $email])) {
            $member = new User();
            $password = $encoder->encodePassword($member, substr(md5(random_bytes(10)), 0, 12));
            $member->setPassword($password);
            $member->setEmail($email);
        } else {
            /** @var ConferenceMember $conferenceMember */
            $conferenceMember = $this->em->getRepository(ConferenceMember::class)->findOneBy([
                'conference'    => $conferenceOrganization->getConference(),
                'user'          => $member,
            ]);

            if ($conferenceMember) {
                $confOrg = $conferenceMember->getConferenceOrganization();

                return $this->badRequest('Пользователь с email: '.$email.' (ID: '.$member->getId().') '
                .'уже участвует в конференции от организации '.$confOrg->getOrganization()->getName().' (ConfOrgID: '.$confOrg->getId().')');
            }
        }

        /** @var RoomType $roomType */
        if (!$roomType = $this->em->find(RoomType::class, $room_type_id)) {
            return $this->notFound('Room type not found.');
        }

        /**
         * Check for unique phone
         * @var User $memberByPhone
         */
        $memberByPhone = $this->em->getRepository(User::class)->findOneBy(['phone' => $phone]);
        if ($memberByPhone && ($memberByPhone !== $member)) {
            return $this->badRequest('Пользователь (ID: '.$memberByPhone->getId().') "'
                .$memberByPhone->getFullName().'" имеет указанный телефон.');
        }

        $member->setFirstName($first_name);
        $member->setLastName($last_name);
        $member->setMiddleName($middle_name);
        $member->setPhone($phone);
        $member->setPost($post);
        $member->setSex($sex);
        $member->setRepresentative($representative);
        $member->setOrganization($conferenceOrganization->getOrganization());

        $this->em->persist($member);

        $conferenceMember = new ConferenceMember();
        $conferenceMember->setUser($member);
        $conferenceMember->setConferenceOrganization($conferenceOrganization);
        $conferenceMember->setConference($conferenceOrganization->getConference());
        $conferenceMember->setArrival($arrival ? new \DateTime($arrival) : null);
        $conferenceMember->setLeaving($leaving ? new \DateTime($leaving) : null);
        $conferenceMember->setCarNumber($car_number);
        $conferenceMember->setRoomType($roomType);

        $this->em->persist($conferenceMember);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_member/{id}", requirements={"id":"\d+"}, methods={"PUT"}, name="update")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function update($id)
    {
        $first_name = $this->requestData['first_name'] ?? null;
        $last_name = $this->requestData['last_name'] ?? null;
        $middle_name = $this->requestData['middle_name'] ?? null;
        $post = $this->requestData['post'] ?? null;
        $phone = isset($this->requestData['phone']) ? preg_replace('/[^0-9]/', '', $this->requestData['phone']) : null;
        $email = $this->requestData['email'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $car_number = $this->requestData['car_number'] ?? null;
        $arrival = $this->requestData['arrival'] ?? null;
        $leaving = $this->requestData['leaving'] ?? null;
        $representative = isset($this->requestData['representative']) ? (bool) $this->requestData['representative'] : false;
        $room_type_id = $this->requestData['room_type_id'] ?? null;

        if (!$first_name || !$last_name || !$phone || !$email || !$room_type_id) {
            return $this->badRequest('Missing required param.');
        }

        /** @var ConferenceMember $conferenceMember */
        if (!$conferenceMember = $this->em->find(ConferenceMember::class, $id)) {
            return $this->notFound('Conference member not found.');
        }

        /** @var RoomType $roomType */
        if (!$roomType = $this->em->find(RoomType::class, $room_type_id)) {
            return $this->notFound('Room type not found.');
        }

        /**
         * If room type was changed then check that member not settlement
         * @var Place $place
         */
        if (
            $roomType !== $conferenceMember->getRoomType()
            && $place = $this->em->getRepository(Place::class)->findOneBy(['conferenceMember' => $conferenceMember])
        ) {
            return $this->badRequest(
                'Участник уже заселен в комнату типа "'.$conferenceMember->getRoomType()->getTitle()
                .'" в номере #'.$place->getRoom()->getApartment()->getNumber().'. Выселите его, чтобы сменить тип комнаты.'
            );
        }

        $member = $conferenceMember->getUser();

        /**
         * Check for unique phone
         * @var User $memberByPhone
         */
        $memberByPhone = $this->em->getRepository(User::class)->findOneBy(['phone' => $phone]);
        if ($memberByPhone && ($memberByPhone !== $member)) {
            return $this->badRequest('Пользователь (ID: '.$memberByPhone->getId().') "'
                .$memberByPhone->getFullName().'" имеет указанный телефон.');
        }

        /**
         * Check for unique email
         */
        $memberByEmail = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($memberByEmail && ($memberByEmail !== $member)) {
            return $this->badRequest('Пользователь (ID: '.$memberByEmail->getId().') "'
                .$memberByEmail->getFullName().'" имеет указанный email.');
        }

        $member->setFirstName($first_name);
        $member->setLastName($last_name);
        $member->setMiddleName($middle_name);
        $member->setEmail($email);
        $member->setPhone($phone);
        $member->setPost($post);
        $member->setSex($sex);
        $member->setRepresentative($representative);

        $this->em->persist($member);

        $conferenceMember->setArrival($arrival ? new \DateTime($arrival) : null);
        $conferenceMember->setLeaving($leaving ? new \DateTime($leaving) : null);
        $conferenceMember->setCarNumber($car_number);
        $conferenceMember->setRoomType($roomType);

        $this->em->persist($conferenceMember);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_member/{id}", requirements={"id":"\d+"}, methods={"DELETE"}, name="delete")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var ConferenceMember $conferenceMember */
        if (!$conferenceMember = $this->em->find(ConferenceMember::class, $id)) {
            return $this->notFound('Conference member not found.');
        }

        /**
         * Check for neighbourhood
         * @var ConferenceMember[] $neighbourhoods
         */
        $neighbourhoods = $this->em->getRepository(ConferenceMember::class)
            ->findBy(['neighbourhood' => $conferenceMember]);

        foreach ($neighbourhoods as $neighbourhood) {
            $neighbourhood->setNeighbourhood(null);
            $this->em->persist($neighbourhood);
        }

        $this->em->remove($conferenceMember);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference_member/not_settled")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getNotSettled()
    {
        $year = date('Y');

        /** @var Conference $conference */
        if (!$conference = $this->em->getRepository(Conference::class)->findOneBy(['year' => $year])) {
            return $this->notFound("Conference with year $year not found.");
        }

        /** @var ConferenceMemberRepository $conferenceMemberRepo */
        $conferenceMemberRepo = $this->em->getRepository(ConferenceMember::class);

        $conferenceMemberData = $conferenceMemberRepo->getNotSettled($year, $this->requestData['housing_id'] ?? null);

        return $this->success(['items' => $conferenceMemberData]);
    }
}