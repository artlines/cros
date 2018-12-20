<?php

namespace App\Controller;

use AppBundle\Entity\Setting;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminSettingController extends AbstractController
{
    /**
     * @Route("/admin/settings", name="admin-settings")
     *
     * @param Request $request
     *
     * @return object
     */
    public function index(Request $request)
    {
        /** @var Setting $settings */
        $settings = $this->getDoctrine()
            ->getRepository('App:Setting')
            ->find(1);

        /** @var Form $form */
        $form = $this->createFormBuilder($settings)
            ->add('footer_text', TextareaType::class, array('label' => 'Text will be displayed in footer'))
            ->add('send_pass', TextareaType::class, array('label' => 'Пароль для отправки сообщения участникам организаций'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $faq_text = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($faq_text);
            $em->flush();

            $success = 'Текст сохранен';
        }


        return $this->render('admin/settings.html.twig', array(

            'form' => $form->createView(),
        ));

    }
}
