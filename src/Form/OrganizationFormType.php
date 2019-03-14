<?php

namespace App\Form;

use App\Entity\Participating\Organization;
use App\Validator\InnKpp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganizationFormType extends AbstractType
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
                        'class' => 'noedit cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Name.PlaceHolder',
                    ),
//                    'disabled' => true,
                    'help' => 'Organization.Name.Help',
                    'required' => true,
                    'constraints' => [
                        new InnKpp(),
                    ]
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'label' => 'Organization.City.Label',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.City.PlaceHolder',
                    ),
                    'help' => 'Organization.City.Help',
                    'required' => true,
//                    'constraints' => [new Length(['min' => 3])]
                )
            )
            ->add(
                'address',
                TextType::class,
                array(
                    'label' => 'Organization.Address.Label',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Address.PlaceHolder',
                    ),
                    'help' => 'Organization.Address.Help',
                    'required' => true,
                )
            )
            ->add(
                'newlogo',
                FileType::class,
                array(
                    'label' => 'Organization.Logo.Label',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Logo.PlaceHolder',
                    ),
                    'help' => 'Organization.Logo.Help',
                    'required' => false,
                )
            )

            ->add(
                'inn',
                TextType::class,
                array(
                    'label' => 'Organization.Inn.Label',
                    'attr' => array(
                        'class' => 'noedit inn cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Inn.PlaceHolder',
                    ),
//                    'disabled' => true,
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
                        'class' => 'noedit kpp cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Kpp.PlaceHolder',
                    ),
//                    'disabled' => true,
                    'help' => 'Organization.Kpp.Help',
                    'required' => true,
                )
            )
            ->add(
                'requisites',
                TextareaType::class,
                array(
                    'label' => 'Organization.Requisites.Label',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Requisites.PlaceHolder',
                        'rows' => '8',
                    ),
                    'data' => "Полное наименование организации: \nОГРН: \nЮридический адрес: \nПочтовый адрес: \nБанк: \nБИК: \nК/С: \nР/С:",
                    'help' => 'Organization.Requisites.Help',
                    'required' => true,
                )

            )
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
            'data_class' => Organization::class,
//            'attr' => ['class'=>'row'],
        ]);
    }
}
