<?php

namespace App\Validator;

use App\Entity\Participating\ConferenceMember;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ConferenceMemberFormValidator extends ConstraintValidator
{
    public function validate($object, Constraint $constraint)
    {
        /* @var $constraint App\Validator\ConferenceMemberForm */

        $this->context
            ->buildViolation('Превышен лимит участников на одну организацию' )
            ->atPath("ConferenceMember")
            ->addViolation();
    }
}
