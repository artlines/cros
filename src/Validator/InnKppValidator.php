<?php

namespace App\Validator;


use App\Entity\Participating\ConferenceOrganization;
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

    public function validate($value, Constraint $constraint)
    {
        dump('InnKppValidator', $value, $constraint);
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
            //$co = $repository->findByInnKppIsFinish('4502013089', '450201001', 3);
            $co = $repository->findByInnKppIsFinish($inn, $kpp, $conf_id);
            if($co){
                dump('repos', $co);
                $this->context->buildViolation(/*$constraint->message*/
                    'Организация \'{{ value }}\' уже зарегистрирована'
                )
                    ->setParameter('{{ value }}', $co->getOrganization()->getName())
                    ->atPath('organization.inn')
//            ->setParameter('organization.inn', $value)
                    ->addViolation();
            } else {
                dump('repos', $co);
                $this->context->buildViolation('Все ок, можем продолжать')
                    ->setParameter('{{ value }}', $inn)
                    ->atPath('organization.inn')
//            ->setParameter('organization.inn', $value)
                    ->addViolation();
            }

        }
    }
}
