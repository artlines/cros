<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Place;
use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\User;
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

            $_arrival = $conferenceMember->getArrival();
            $_leaving = $conferenceMember->getLeaving();

            $roomType = $conferenceMember->getRoomType();

            $items[] = [
                'id'            => $conferenceMember->getId(),
                'first_name'    => $member->getFirstName(),
                'last_name'     => $member->getLastName(),
                'middle_name'   => $member->getMiddleName(),
                'post'          => $member->getPost(),
                'phone'         => $member->getPhone(),
                'email'         => $member->getEmail(),
                'sex'           => $member->getSex(),
                'car_number'    => $conferenceMember->getCarNumber(),
                'representative'=> $member->isRepresentative()? 1 : 0,
                'arrival'       => $_arrival ? $_arrival->format('Y-m-d\TH:i') : null,
                'leaving'       => $_leaving ? $_leaving->format('Y-m-d\TH:i') : null,
                'room_type_id'  => $roomType ? $roomType->getId() : null,
                'place'         => $placeInfo,
            ];
        }

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
        $phone = $this->requestData['phone'] ?? null;
        $email = $this->requestData['email'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $car_number = $this->requestData['car_number'] ?? null;
        $arrival = $this->requestData['arrival'] ?? null;
        $leaving = $this->requestData['leaving'] ?? null;
        $representative = isset($this->requestData['representative']) ? (bool) $this->requestData['representative'] : null;
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
                .$memberByPhone->getFirstName().'" имеет указанный телефон.');
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
        $phone = $this->requestData['phone'] ?? null;
        $email = $this->requestData['email'] ?? null;
        $sex = $this->requestData['sex'] ?? null;
        $car_number = $this->requestData['car_number'] ?? null;
        $arrival = $this->requestData['arrival'] ?? null;
        $leaving = $this->requestData['leaving'] ?? null;
        $representative = isset($this->requestData['representative']) ? (bool) $this->requestData['representative'] : null;
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

        $member = $conferenceMember->getUser();

        /**
         * Check for unique phone
         * @var User $memberByPhone
         */
        $memberByPhone = $this->em->getRepository(User::class)->findOneBy(['phone' => $phone]);
        if ($memberByPhone && ($memberByPhone !== $member)) {
            return $this->badRequest('Пользователь (ID: '.$memberByPhone->getId().') "'
                .$memberByPhone->getFirstName().'" имеет указанный телефон.');
        }

        /**
         * Check for unique email
         */
        $memberByEmail = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($memberByEmail && ($memberByEmail !== $member)) {
            return $this->badRequest('Пользователь (ID: '.$memberByEmail->getId().') "'
                .$memberByEmail->getFirstName().'" имеет указанный email.');
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

        $this->em->remove($conferenceMember);
        $this->em->flush();

        return $this->success();
    }
}