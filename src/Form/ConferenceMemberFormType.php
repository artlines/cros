<?php

namespace App\Form;

use App\Entity\Abode\RoomType;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
//            ->add('roomType')
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

                        'class' => 'cs-theme-color-gray-dark-v3 select-roomtype',
                    ],
                    'required' => false,
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
// Совместное проживание ???
            ->add(
                'neighbourhood',
                ChoiceType::class,
                [
                    'label' => 'Совместное проживание', // 'Member.sex.Label',
                    'required' => true,
                    'choices'  => [
                        "Нет" => null,
                        "Да"  => null,
                    ],
                    'data' => 'Да' , // default
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConferenceMember::class,
        ]);
    }
}
