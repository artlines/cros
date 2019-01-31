<?php

namespace App\Form;

use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceMemberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user')
//            ->add('conference')
//   !!!            ->add('conferenceOrganization')
            ->add('roomType')
            ->add(
                'carNumber',
                TextType::class,
                [
                    'label' => 'Номер автомобиля',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'А001АА 00',
                        'pattern' => '[А-Яа-яA-Za-z]{1,1}[0-9]{3,3}[А-Яа-яA-Za-z]{2,2}[ ][0-9]{2,3}',
                    ),
                    'required' => false,
                ]
            )

            ->add(
                'arrival',
                DateType::class,
                [
                    'label' => 'Ранний заезд',
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control input-inline datetimepicker cs-theme-color-gray-dark-v3',
                        'data-provide' => 'datetimepicker',
                        'html5' => false,
                    ],
                    'required' => false,
                ]
            )

            ->add(
                'leaving',
                DateType::class,
                [
                    'label' => 'Поздний выезд',
                    'widget' => 'single_text',
                    'attr' => [
                        'class' => 'form-control input-inline datetimepicker cs-theme-color-gray-dark-v3',
                        'data-provide' => 'datetimepicker',
                        'html5' => false,
                    ],
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
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'member',
                EntityType::class,
                [
                    'label' => 'member usre',
                    'class' => User::class,
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                    'required' => false,
                ]
            )

            ->add(
                'neighbourhood',
                TextType::class,
                [
                    'label' => 'Номер автомобиля',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'А001АА 00',
                        'pattern' => '[А-Яа-яA-Za-z]{1,1}[0-9]{3,3}[А-Яа-яA-Za-z]{2,2}[ ][0-9]{2,3}',
                    ),
                    'required' => false,
                ]
            )
// Совместное проживание ???
//            ->add(
//                'neighbourhood',
//                EntityType::class,
//                [
//                    'class' => ConferenceMember::class,
//                    'label' => 'Совместное проживание',
//                    'attr' => [
//                        'class' => 'cs-theme-color-gray-dark-v3',
//                        'style' => 'margin-top: 37px;'
//                    ],
//                    'required' => false,
//                ]
//            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConferenceMember::class,
        ]);
    }
}
