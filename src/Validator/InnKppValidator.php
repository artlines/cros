<?php

namespace App\Validator;


use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class InnKppValidator extends ConstraintValidator
{
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function validate($value, Constraint $constraint)
    {
        dump('InnKppValidator',$value ,$constraint);
        /* @var $constraint App\Validator\InnKpp */
if( is_object($value)){
           $em = $this->registry->getManagerForClass(\get_class($value));
            if (!$em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with an entity of class "%s".', \get_class($entity)));
            }
	    $repository = $em->getRepository(\get_class($value));
	    $co = $repository->findByInnKppIsFinish('4502013089','450201001',3);
	    $co->getOrganization()->getInn();
	     dump('repos',$co);
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $co->getOrganization()->getName())
            ->atPath('organization.inn')
//            ->setParameter('organization.inn', $value)
            ->addViolation();
}
    }
}
