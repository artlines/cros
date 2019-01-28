<?php

namespace App\Controller;

use App\Form\ConferenceRegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceRegistrationController extends AbstractController
{
    /**
     * @Route("/conference/registration", name="conference_registration")
     */
    public function index()
    {
//        $form = $this->createForm(MyFrmType::class,null,['attr'=>['class'=>'row']]);
        $form = $this->createForm(ConferenceRegistrationFormType::class);

        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
        ]);
    }
}

