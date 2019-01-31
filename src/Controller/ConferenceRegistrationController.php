<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Form\ConferenceOrganizationFormType;
use App\Form\OrganizationFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConferenceRegistrationController extends AbstractController
{
    private function getRandomPassword()
    {
        return substr( md5(random_bytes(10)),-6);
    }
    /**
     * @Route("/conference/registration/{hash}", name="conference_registration_hash")
     * @Route("/conference/registration", name="conference_registration")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $hash = $request->get('hash');
        if( $hash ) {
            $ConferenceOrganization = $this->getDoctrine()
                ->getRepository(ConferenceOrganization::class)
                ->findOneBy(['inviteHash'=>$hash]);

        } else {
            $ConferenceOrganization = null;
        }
//        $form = $this->createForm(MyFrmType::class,null,['attr'=>['class'=>'row']]);
        $form = $this->createForm(
            ConferenceOrganizationFormType::class, $ConferenceOrganization);

        $form->handleRequest($request);
        $Conference = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findOneBy(['year' => date("Y")]);
        /** @var Conference $Conference */
        /** @var ConferenceOrganization $ConferenceOrganization */
//        $ConferenceOrganization = $form->getData();
//        dump($ConferenceOrganization);
//        /** @var Organization $check_org */
//        $check_org = $this->getDoctrine()
//            ->getRepository( ConferenceOrganization::class)
//            ->findByInnKppIsFinish(
//                $ConferenceOrganization->getOrganization()->getInn(),
//                $ConferenceOrganization->getOrganization()->getKpp(),
//                $ConferenceOrganization->getConference()->getId()
//            );


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ConferenceOrganization $ConferenceOrganization */
            $ConferenceOrganization = $form->getData();
            $em  = $this->getDoctrine()->getManager();
            $em->getConnection()->beginTransaction();

            //setup Conference for conferenceMembers
            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember ) {
                $user = $conferenceMember->getUser();
                $conferenceMember->setUser(null);
                /** @var User $oldUser */
                $oldUser = $em->getRepository(User::class)
                    ->findOneBy(['email' =>$user->getEmail() ]);
                if( $oldUser ){
                    $em->remove($user);
                    $conferenceMember->setUser($oldUser);
                    $user = $oldUser;
                }
                $organization = $ConferenceOrganization->getOrganization();

                $user->setOrganization($organization);


                $em->persist($organization);


                $conference = $ConferenceOrganization->getConference();
                $conferenceMember->setConference($conference);
                $conferenceMember->setConferenceOrganization($ConferenceOrganization);
                $em->persist($ConferenceOrganization); // !!DUP
                $em->flush();
                $em->flush();
            }
            // TODO: get duplicate  Organization
//            $em->persist($ConferenceOrganization->getOrganization());
            $em->flush();

            $em->getConnection()->commit();

            return $this->render('conference_registration/registration_success.html.twig', [
                'ConferenceOrganization' => $ConferenceOrganization,
            ]);
        }


        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
            'LimitUsersByOrg' => $Conference->getLimitUsersByOrg(),
            'LimitUsersGlobal' => $Conference->getLimitUsersGlobal(),
            'Users'   => 0, // TODD: get real users value
        ]);
    }
}

