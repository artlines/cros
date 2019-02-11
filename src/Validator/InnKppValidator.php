<?php

namespace App\Validator;


use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Repository\ConferenceMemberRepository;
use App\Repository\ConferenceOrganizationRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class InnKppValidator extends ConstraintValidator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function validate($value, Constraint $constraint)
    {
//        dump('InnKppValidator', $value, $constraint);
        /* @var $constraint InnKpp */
        if (is_object($value) && $value instanceof ConferenceOrganization) {
            $em = $this->registry->getManagerForClass(\get_class($value));
            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', \get_class($entity)));
            }
            $repository = $em->getRepository(\get_class($value));
            /** @var ConferenceOrganization $value */
            $inn = $value->getOrganization()->getInn();
            $kpp = $value->getOrganization()->getKpp();
            $conf_id = $value->getConference()->getId();

            /** @var ConferenceOrganizationRepository $repository */

            $co = $repository->findByInnKppIsFinish($inn, $kpp, $conf_id);
            if($co){
                $this->context->buildViolation(/*$constraint->message*/
                    'Организация \'{{ value }}\' уже зарегистрирована'
                )
                    ->setParameter('{{ value }}', $co->getOrganization()->getName())
                    ->atPath('organization.inn')
                    ->addViolation();
            }

            $roomTypes = $em
                ->getRepository(RoomType::class)
                ->findAllFreeForConference($conf_id);
            /** @var RoomType $roomType */
            $arFreeRooms = [];
            foreach ($roomTypes as list($RoomType, $used, $rooms)){
                /** @var RoomType $RoomType */
                $arFreeRooms[$RoomType->getId()] = $RoomType->getMaxPlaces()*$rooms - $used;
            }
            $usedEmail = [];
            foreach ( $value->getConferenceMembers() as $key => $conferenceMember ){

                $roomTypeId = $conferenceMember->getRoomType()->getId();
                if (isset($arFreeRooms[$roomTypeId]) and $arFreeRooms[$roomTypeId]>0) {
                    // вычитаем предполагаемое заселение.
                    $arFreeRooms[$roomTypeId] -= 1;
                } else {
                    $this->context
                        ->buildViolation('Не достаточно свободных номеров' )
                        ->atPath("ConferenceMembers[{$key}].RoomType")
                        ->addViolation();
                }


                if($conferenceMember->getId()>0) continue;
                $email = $conferenceMember->getUser()->getEmail();
                $repository = $em->getRepository(ConferenceMember::class);
                /** @var ConferenceOrganization $value */

                /** @var ConferenceMemberRepository $repository */
                $cm = $repository->findConferenceMemberByEmail($conf_id,$email);
                if ($cm) {
                    $this->context
                        ->buildViolation('Пользователь с такой почтой уже зарегистрирован' )
                        ->atPath("ConferenceMembers[{$key}].user.email")
                        ->addViolation();
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))  {
                    $this->context
                        ->buildViolation('Не верный формат почты' )
                        ->atPath("ConferenceMembers[{$key}].user.email")
                        ->addViolation();
                } elseif (isset($usedEmail[$email])) {
                    $this->context
                        ->buildViolation('Почтовый ящик должен быть уникальным у каждого участника' )
                        ->atPath("ConferenceMembers[{$key}].user.email")
                        ->addViolation();
                } else {
                    // Пометить почту как используемую
                    $usedEmail[$email] = $email;
                }
            }
        }
    }
}
