<?php

namespace App\Form;

use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
/*
При входе на страницу регистрации показывать форму с полями:

+ Наименование организации (обязательное)
Логотип компании - загрузка файла - png
+ Город (обязательное)
+ Адрес (обязательное)
+ ИНН (обязательное)
+ КПП (обязательное)
+ Реквизиты (с подстановкой текста -
  Полное наименование организации,
  ОГРН, Юридический адрес, Почтовый адрес, Банк, БИК, К/С, Р/С) (обязательное)
Комментарий
*/

        $builder
//            ->add('dueDate', null, ['widget' => 'single_text'])
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'Organization.Name.Label',
                    'attr' => array(
                        'class' => 'col-md-6 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Name.PlaceHolder',
                    ),
                    'help' => 'Organization.Name.Help',
                    'required' => true,
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'label' => 'Organization.City.Label',
                    'attr' => array(
                        'class' => 'col-md-6 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.City.PlaceHolder',
                    ),
                    'help' => 'Organization.City.Help',
                    'required' => true,
                )
            )
            ->add(
                'address',
                TextType::class,
                array(
                    'label' => 'Organization.Address.Label',
                    'attr' => array(
                        'class' => 'col-md-6 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Address.PlaceHolder',
                    ),
                    'help' => 'Organization.Address.Help',
                    'required' => true,
                )
            )
            ->add(
                'inn',
                TextType::class,
                array(
                    'label' => 'Organization.Inn.Label',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Inn.PlaceHolder',
                    ),
                    'help' => 'Organization.Inn.Help',
                    'required' => true,
                )
            )
            ->add(
                'kpp',
                TextType::class,
                array(
                    'label' => 'Organization.Kpp.Label',
                    'attr' => array(
                        'class' => 'col-md-3 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Kpp.PlaceHolder',
                    ),
                    'help' => 'Organization.Kpp.Help',
                    'required' => true,
                )
            )

//            ->add('name')
//            ->add('city')
            ->add(
                'requisites',
                TextareaType::class,
                array(
                    'label' => 'Organization.Requisites.Label',
                    'attr' => array(
                        'class' => 'col-md-6 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Requisites.PlaceHolder',
                        'rows' => '8',
                    ),
                    'data' => "Полное наименование организации: \nОГРН: \nЮридический адрес: \nПочтовый адрес: \nБанк: \nБИК: \nК/С: \nР/С:",
                    'help' => 'Organization.Requisites.Help',
                    'required' => true,
                )

            )
            ->add(
                'comment',
                TextareaType::class,
                array(
                    'label' => 'Organization.Comment.Label',
                    'attr' => array(
                        'class' => 'col-md-6 cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Comment.PlaceHolder',
                        'rows' => '4',
                    ),
                    'help' => 'Organization.Comment.Help',
                    'required' => false,
                )
            )
            ->add(
                'member',
                CollectionType::class, [
                'entry_type' => MemberFormType::class,
                'allow_add' => true,
                'prototype' => true,

//                'prototype_data' => new User()
            ])
            ->add('tags', CollectionType::class, [
                'entry_type' => TextType::class,
                'allow_add' => true,
                'prototype' => true,
                'prototype_data' => 'New Tag Placeholder',
            ])
//            ->add(
//                $builder->create(
//                    'member2',
//                    MemberFormType::class,
//                    ['by_reference' => true]
//                ),
//                [
//                    'allow_add' => true,
//                ]
//            )
            ->add('save', SubmitType::class)

//            ->add('isActive')
//            ->add('hidden')
//            ->add('country')
//            ->add('typePerson')
//            ->add('email')
//            ->add('createdAt')
            // ['attr'=>['class'=>'row']]
//            ->setAttribute('id','row')
        ;


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
//            'attr' => ['class'=>'row'],
        ]);
    }
}
