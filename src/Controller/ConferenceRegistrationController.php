<?php

namespace App\Controller;

use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Form\ConferenceRegistrationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceRegistrationController extends AbstractController
{
    /**
     * @Route("/conference/registration", name="conference_registration")
     */
    public function index(Request $request)
    {
//        $form = $this->createForm(MyFrmType::class,null,['attr'=>['class'=>'row']]);
        $form = $this->createForm(ConferenceRegistrationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($form->getData());
        }

        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
        ]);
    }
}

