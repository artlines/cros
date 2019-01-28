<?php

namespace App\Form;

use App\Entity\Participating\ParticipationClass;
use App\Entity\Participating\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
/*
Кнопка "Добавить участника". По клику - показать форму добавления участника с полями
(без перезагрузки страниц, таблицы participating.conference_member, participating.member):
+ Фамилия (обязательное)
+ Имя (обязательное)
+ Отчество
+ Должность
Пол (мо умолчанию - мужской)
Фото
+ Телефон (обязательное)
+ E-mail (обязательное)
+ Номер автомобиля
+ Ранний заезд
+ Поздний выезд
Класс участия (выпадающий список - как сейчас)
Совместное проживание (checkbox, при его установке - показывается выпадающий список уже добавленных на странице участников).
Представитель организации (checkbox). Должен быть минимум один на организацию. Представитель организации обязательно должен подтвердить свой e-mail - отправлять код на e-mail сразу после ввода (onBlur).
 */


        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => 'Имя',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => true,
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => 'Фамилия',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => true,
                ]
            )
            ->add(
                'middleName',
                TextType::class,
                [
                    'label' => 'Отчество',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => false,
                ]
            )
            ->add(
                'post',
                TextType::class,
                [
                    'label' => 'Должность',
                    'required' => false,
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                ]
            )
            ->add(
                'sex',
                ChoiceType::class,
                [
                    'label' => 'Пол', // 'Member.sex.Label',
                    'required' => false,
                    'choices'  => [
                        'Мужской' => User::SEX__MAN,
                        'Женский' => User::SEX__WOMAN,
                    ],
                    'data' => User::SEX__MAN, // default
                    'attr' => [
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ],
                ]
            )

            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Телефон',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => true,
                ]
            )
            ->add(
                'email',
                TextType::class,
                [
                    'label' => 'E-Mail',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => true,
                ]
            )

            ->add(
                'carNumber',
                TextType::class,
                [
                    'label' => 'Номер автомобиля',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ),
                    'required' => false,
                ]
            )

            ->add(
                'earlyIn',
                CheckboxType::class,
                [
                    'label' => 'Ранний заезд',
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                    'required' => false,
                ]
            )

            ->add(
                'laterOut',
                CheckboxType::class,
                [
                    'label' => 'Поздний выезд',
                    'attr' => [
                        'class' => 'cs-theme-color-gray-dark-v3',
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'laterOut',
                EntityType::class,
                [
                    'class' => ParticipationClass::class,
                    'label' => 'Класс участия',
                    'attr' => [
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                    ],
                    'required' => false,
                ]
            )



            //            ->add('isActive')
//            ->add('password')
//            ->add('telegram')
//            ->add('roles')
            ->add('nickname')
//            ->add('sex')
            ->add('representative')
//            ->add('createdAt')
//            ->add('organization')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['class'=>'cs-theme-color-gray-dark-v3']
        ]);
    }
}
