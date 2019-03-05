<?php

namespace App\Validator;

use App\Entity\Participating\ConferenceMember;
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

            $this->context
                ->buildViolation('Превышен лимит участников на одну организацию')
                ->atPath("ConferenceMember")
                ->addViolation();

            $email = $conferenceMember->getUser()->getEmail();
            $em = $this->registry->getManagerForClass(\get_class($conferenceMember));
            $repository = $em->getRepository(ConferenceMember::class);
            /** @var ConferenceOrganization $value */

            /** @var ConferenceMemberRepository $repository */
            $conf_id = $conferenceMember->getConference()->getId();
            $cm = $repository->findConferenceMemberByEmail($conf_id, $email);

            if ($cm) {
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
        }
    }
}
