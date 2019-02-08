<?php

namespace App\Form;

use App\Entity\Participating\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content',TextareaType::class,[
                'label' => 'Новый коментарий',
                'attr' => [
                    'class' => 'cs-theme-color-gray-dark-v3',
                    'placeholder' => 'Ваш комментарий...',
                    'rows' => '8',
                ]
            ])
//            ->add('isPrivate')
//            ->add('createdAt')
//            ->add('user')
//            ->add('conferenceOrganization')
            ->add('save', SubmitType::class, [
                'label' => 'Оставить коментарий',
                'attr' => [
                    'class' => 'u-btn-darkblue cs-font-size-13 cs-px-10 cs-py-10 mb-0 cs-mt-15'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
