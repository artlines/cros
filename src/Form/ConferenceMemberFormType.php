<?php

namespace App\Form;

use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\User;
use App\Repository\Abode\RoomTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceMemberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user')
//            ->add('conference')
//   !!!            ->add('conferenceOrganization')
//            ->add('roomType')
            ->add(
                'carNumber',
                TextType::class,
                [
                    'label' => 'Номер автомобиля',
                    'attr' => array(
                        'class' => 'carNumber cs-theme-color-gray-dark-v3',
                        'placeholder' => 'А001АА 00',
//                        'pattern' => '[А-Яа-яA-Za-z]{1,1}[0-9]{3,3}[А-Яа-яA-Za-z]{2,2}[ ][0-9]{2,3}',
                    ),
                    'help' => 'Если Вы приедете на личном транспорте, укажите его госуУчастник рственный номер',
                    'required' => false,
                ]
            )

            ->add(
                'arrival',
                DateTimeType::class,
                [
                    'label' => 'Ранний заезд',
                    'widget' => 'single_text',
                    'format' => 'yyyy.MM.dd HH:mm',
                    'attr' => [
                        'class' => 'form-control input-inline datetimepicker cs-theme-color-gray-dark-v3',
                        'data-provide' => 'datetimepicker',
                    ],
                    'html5' => false,
                    'help' => 'Укажите, если Вы приедете заранее',
                    'required' => false,
                ]
            )

            ->add(
                'leaving',
                DateTimeType::class,
                [
                    'label' => 'Поздний выезд',
                    'widget' => 'single_text',
                    'format' => 'yyyy.MM.dd HH:mm',
                    'attr' => [
//                        'type' => "text",
                        'class' => 'datetime form-control input-inline datetimepicker cs-theme-color-gray-dark-v3',
                        'data-provide' => 'datetimepicker',
                    ],
                    'html5' => false,
                    'help' => 'Укажите, если Вы планируете задержаться',
                    'required' => false,
                ]
            )
            ->add(
                'RoomType',
                EntityType::class,
                [
                    'class' => RoomType::class,
                    'label' => 'Класс участия',
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3 select-roomtype',
                    ],
                    'required' => true,
//                    'choice_label' => function (RoomType $entity = null) {
//                        return $entity->getTitle();
//                    },
//                    'query_builder' => function (RoomTypeRepository $conferenceRepository) {
//                        return $conferenceRepository->findAllFreeForConferenceQueryBuilder(372)
//                        // createQueryBuilder('c')
////                            ->andWhere('c.registrationStart <= :registrationStart')
////                            ->andWhere('c.registrationFinish >= :registrationFinish')
////                            ->setParameters([
////                                'registrationStart' => new \DateTime(),
////                                'registrationFinish' => new \DateTime()
////                            ])
//                            ;
//                    },
                ]
            )


            ->add(
                $builder->create(
                    'user',
                    MemberFormType::class,
                    ['by_reference' => true]
                )
                    ->remove('save')
            )

// Совместное проживание ???
            ->add(
                'neighbourhood',
                ChoiceType::class,
                [
                    'label' => 'Совместное проживание', // 'Member.sex.Label',
                    'required' => false,
                    'choices'  => [
                        "Нет" => '',
                        "Участник 1"  => 0,
                        "Участник 2"  => 1,
                        "Участник 3"  => 2,
                        "Участник 4"  => 3,
                        "Участник 5"  => 4,
                        "Участник 6"  => 5,
                        "Участник 7"  => 6,
                        "Участник 8"  => 7,
                        "Участник 9"  => 8,
                        "Участник 10"  => 9,
                    ],
                    'data' => 'Нет' , // default
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3 select-neighbourhood',
                    ],
                    'help' => 'Выбор участика, для размещения в одном номере'
                ]
            );
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // ... adding the name field if needed

//            dd($event);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConferenceMember::class,
        ]);
    }
}
