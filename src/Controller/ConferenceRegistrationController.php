<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Form\ConferenceOrganizationFormType;
use App\Form\OrganizationFormType;
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
        $form = $this->createForm(ConferenceOrganizationFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /** @var ConferenceOrganization $ConferenceOrganization */
            $ConferenceOrganization = $form->getData();
            $ConferenceOrganization->setConference($em->getReference(Conference::class, 272 ));
            $em->getConnection()->beginTransaction();
            $em->persist($ConferenceOrganization);
            // TODO: get duplicate  Organization
            $em->persist($ConferenceOrganization->getOrganization());
            $em->flush();
            $em->getConnection()->commit();
            dd($ConferenceOrganization);
        }

        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
        ]);
    }
}

