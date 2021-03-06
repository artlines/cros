<?php

namespace App\Form;

use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                        'placeholder' => 'Ваш комментарий...',
                        'rows' => '4',
                    ),
                    'help' => 'Дополнительная информация',
                    'required' => false,
                )
            )
            ->add(
                $builder->create(
                    'organization',
                    OrganizationFormType::class,
                    ['by_reference' => true]
                )
                ->remove('save')
            )

            ->add(
                'ConferenceMembers',
                CollectionType::class, [
                'label' => 'ConferenceMembers',
                'entry_type' => ConferenceMemberFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,

//                'prototype_data' => new User()
            ])



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
                            ->andWhere('c.year = :year')
                            ->setParameters([
                                'year' => date("Y"),
                            ]);
                    },
//                    'choice_label' => 'id',
                ]

            )
//            ->add('invitedBy')
        ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
                if (!$data) {
                    return;
                }

                /** @var ConferenceOrganization $data */

                // Заполнение дублирующих значений в форме  conferenceOrganization и conference
                // Перекрестных ссылки
                foreach ($data['ConferenceMembers'] as $key => $user){
                    $data['ConferenceMembers'][$key]['conference'] = $data['conference'];
                    if (isset($data['conferenceOrganization'])) {
                        $data['ConferenceMembers'][$key]['conferenceOrganization'] = $data['conferenceOrganization'];
                    }
                }
                $event->setData($data);
                /** @var ConferenceOrganization $conferenceOrganization */
        })
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Завершить регистрацию',
                    'attr' => [
                        'class' => 'reg-save u-btn-darkblue cs-font-size-13 cs-px-10 cs-py-10 mb-0 cs-mt-15'
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
