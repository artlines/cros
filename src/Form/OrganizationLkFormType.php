<?php

namespace App\Form;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Repository\UserRepository;
use App\Validator\InnKpp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class OrganizationLkFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add(
                'newlogo',
                FileType::class,
                array(
                    'label' => 'Organization.Logo.Label',
                    'attr' => array(
                        'class' => 'cs-theme-color-gray-dark-v3',
                        'placeholder' => 'Organization.Logo.PlaceHolder',
                        'accept' => '.jpg,.jpeg,.png'
                    ),
                    'help' => 'Organization.Logo.Help',
                    'required' => false,
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
