<?php

namespace App\Validator;

use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Repository\Abode\RoomTypeRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConferenceMemberFormValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($conferenceMember, Constraint $constraint)
    {
        if (is_object($conferenceMember) || $conferenceMember instanceof ConferenceMember) {
            /* @var $constraint App\Validator\ConferenceMemberForm */

            $email = $conferenceMember->getUser()->getEmail();
            $em = $this->registry->getManagerForClass(\get_class($conferenceMember));
            $repository = $em->getRepository(ConferenceMember::class);
            /** @var ConferenceOrganization $value */

            /** @var ConferenceMemberRepository $repository */
            $conf_id = $conferenceMember->getConferenceOrganization()->getConference()->getId();
            $cm = $repository->findConferenceMemberByEmail($conf_id, $email);

            if ($cm and $cm != $conferenceMember) {
                $this->context
                    ->buildViolation('Пользователь с такой почтой уже зарегистрирован')
                    ->atPath("user.email")
                    ->addViolation();
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->context
                    ->buildViolation('Неверный формат почты')
                    ->atPath("user.email")
                    ->addViolation();
            }
            $conference = $conferenceMember
                ->getConferenceOrganization()
                ->getConference();
            $count = $conferenceMember->getConferenceOrganization()->getConferenceMembers()->count();
            $limit = $conference->getLimitUsersByOrg();
            if ($count >= $limit
                // Проверка что создание участника ( исключение, для редактирования )
                and $conferenceMember->getId() < 1
            ) {
                $this->context
                    ->buildViolation('Превышен лимит участников на одну организацию')
                    ->atPath("roomType")
                    ->addViolation();
            }
            $members_count = $conference->getConferenceMembers()->count();
            $conferenceLimit = $conference->getLimitUsersGlobal();
            if ($members_count >= $conferenceLimit
                // Проверка что создание участника ( исключение, для редактирования )
                and $conferenceMember->getId() < 1
            ) {
                $this->context
                    ->buildViolation('Превышен лимит участников на конференцию')
                    ->atPath("roomType")
                    ->addViolation();
            }

            /** @var RoomTypeRepository $roomTypeRepo */
            $roomTypeRepo = $em->getRepository(RoomType::class);
            $roomTypesInfo = $roomTypeRepo->getSummaryInformation();

            $arFreePlaces = [];
            foreach ($roomTypesInfo as $type) {
                $arFreePlaces[$type['room_type_id']] = $type['total'] - $type['busy'] - $type['reserved'];
            }

            if ($conferenceMember->getRoomType()) {
                $roomTypeId = $conferenceMember->getRoomType()->getId();
                if (
                    (
                        !isset($arFreePlaces[$roomTypeId])
                        or $arFreePlaces[$roomTypeId] < 1
                    )
                    // Проверка что создание участника ( исключение, для редактирования )
                    and $conferenceMember->getId() < 1

                ) {
                    $this->context
                        ->buildViolation('Недостаточно свободных номеров')
                        ->atPath("roomType")
                        ->addViolation();
                }
            }


        }
    }
}
