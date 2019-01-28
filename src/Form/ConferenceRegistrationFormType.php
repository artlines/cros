<?php

namespace App\Form;

use App\Entity\Participating\Organization;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConferenceRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
//                    'label' => 'Название организации',
                    'attr' => array(
                        'class' => 'col-md-6',
                        'placeholder' => 'Ёлки-телеком',
                        'data-helper' => 'Ваш основной Торговый знак, будет использоваться на бейджах и визитках'
                    ),
                    'help' => '.help',
                    'required' => true
                )
            )
            ->add('city',
                TextType::class,
                array(
                    'label' => 'City',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3 col-md-6'
                    )
                )
            )
//            ->add('name')
            ->add('email')
//            ->add('city')
            ->add('requisites')
            ->add('address')
            ->add('isActive')
            ->add('inn')
            ->add('kpp')
            ->add('hidden')
            ->add('comment')
            ->add('country')
            ->add('typePerson')
            ->add('createdAt')
            // ['attr'=>['class'=>'row']]
//            ->setAttribute('id','row')
        ;
        $options['attr']['class'] = 'row';


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organization::class,
            'attr' => ['class'=>'row'],
        ]);
    }
}
