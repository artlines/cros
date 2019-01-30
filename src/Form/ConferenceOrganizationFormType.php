<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceOrganizationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'notes',
                TextareaType::class,
                array(
                    'label' => 'Комментарий',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Ваш коментарий...',
                        'rows' => '4',
                    ),
                    'help' => 'Дополнительная информация',
                    'required' => false,
                )
            )
//            ->add('sponsor')
//            ->add('finish')
//            ->add('approved')
//            ->add('inviteHash')
            ->add(
                $builder->create(
                    'organization',
                    OrganizationFormType::class,
                    ['by_reference' => true]
//                EntityType::class,
//                [
//                    'class' => Organization::class,
//                    'label' => 'Organization class',
//                    'attr' => [
//                        'class' => 'cs-theme-color-gray-dark-v3',
//                    ],
//                    'required' => true,
//                ]
                )
                ->remove('save')
            )
            ->add(
                'conference',
                EntityType::class,
                [
                    'label' => 'Конференция',
                    'class' => Conference::class,
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                    'required' => true,
//                    'choices' =>  Conference::
//                    'help' => 'Дополнительная информация',
                    'query_builder' => function (ConferenceRepository $conferenceRepository) {
                        return $conferenceRepository->createQueryBuilder('c')
                            ->andWhere('c.registrationStart <= :registrationStart')
                            ->andWhere('c.registrationFinish >= :registrationFinish')
                            ->setParameters([
                                'registrationStart' => new \DateTime(),
                                'registrationFinish' => new \DateTime()
                            ]);
                    },
//                    'choice_label' => 'id',
                ]

            )
//            ->add('invitedBy')
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Сохранить',
                    'attr' => [
                        'class' => 'u-btn-darkblue cs-font-size-13 cs-px-10 cs-py-10 mb-0 cs-mt-15'
                    ]
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConferenceOrganization::class,
        ]);
    }
}
