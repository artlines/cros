<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Setting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminSettingController extends Controller
{
    /**
     * @Route("/admin/settings", name="admin-settings")
     *
     * @param Request $request
     *
     * @return object
     */
    public function indexAction(Request $request)
    {
        /** @var Setting $settings */
        $settings = $this->getDoctrine()
            ->getRepository('AppBundle:Setting')
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
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'form' => $form->createView(),
        ));

    }
}
