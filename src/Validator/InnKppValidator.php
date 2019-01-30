<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InnKppValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        dump('InnKppValidator',$value ,$constraint);
        /* @var $constraint App\Validator\InnKpp */

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
//            ->setParameter('organization.inn', $value)
            ->addViolation();
    }
}
