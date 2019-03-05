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

//            $this->context
//                ->buildViolation('DEBUG STOP')
//                ->atPath("ConferenceMember")
//                ->addViolation();

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
                    ->buildViolation('Не верный формат почты')
                    ->atPath("user.email")
                    ->addViolation();
            }

            /** @var RoomTypeRepository $roomTypeRepo */
            $roomTypeRepo = $em->getRepository(RoomType::class);
            $roomTypesInfo = $roomTypeRepo->getSummaryInformation();

            $arFreePlaces = [];
            foreach ($roomTypesInfo as $type){
                $arFreePlaces[$type['room_type_id']] = $type['total'] - $type['busy'] - $type['reserved'];
            }

            $count = $conferenceMember->getConferenceOrganization()->getConferenceMembers()->count();
            $limit = $conferenceMember->getConference()->getLimitUsersByOrg();
            if ($count >= $limit and $conferenceMember->getId()<1) {
                $this->context
                    ->buildViolation('Превышен лимит участников на одну организацию' )
                    ->atPath("roomType")
                    ->addViolation();
            }

            if ($conferenceMember->getRoomType()) {
                $roomTypeId = $conferenceMember->getRoomType()->getId();
                if (isset($arFreePlaces[$roomTypeId]) and $arFreePlaces[$roomTypeId] > 0) {
                    // вычитаем предполагаемое заселение.
                    //$arFreePlaces[$roomTypeId] -= 1;
                } else {
                    $this->context
                        ->buildViolation('Не достаточно свободных номеров')
                        ->atPath("roomType")
                        ->addViolation();
                }
            }


        }
    }
}
