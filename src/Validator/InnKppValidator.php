<?php

namespace App\Validator;


use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Repository\Abode\RoomTypeRepository;
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
                    'Организация \'{{ value }}\' уже зарегистрирована'.$inn.' - '. $kpp. $co ->getId()
                )
                    ->setParameter('{{ value }}', $co->getOrganization()->getName())
                    ->atPath('organization.inn')
                    ->addViolation();
            }

            /** @var RoomTypeRepository $roomTypeRepo */
            $roomTypeRepo = $em->getRepository(RoomType::class);
            $roomTypesInfo = $roomTypeRepo->getSummaryInformation();

            $arFreePlaces = [];
            foreach ($roomTypesInfo as $type){
                $arFreePlaces[$type['room_type_id']] = $type['total'] - $type['busy'] - $type['reserved'];
            }

            $usedEmail = [];
            foreach ( $value->getConferenceMembers() as $key => $conferenceMember ){
                if ($key >= $value->getConference()->getLimitUsersByOrg() ) {
                    $this->context
                        ->buildViolation('Превышен лимит участников на одну организацию' )
                        ->atPath("ConferenceMembers[{$key}].RoomType")
                        ->addViolation();

                }
                $roomTypeId = $conferenceMember->getRoomType()->getId();
                if (isset($arFreePlaces[$roomTypeId]) and $arFreePlaces[$roomTypeId]>0) {
                    // вычитаем предполагаемое заселение.
                    $arFreePlaces[$roomTypeId] -= 1;
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
