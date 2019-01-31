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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConferenceRegistrationController extends AbstractController
{
    private function getRandomPassword()
    {
        return substr( md5(random_bytes(10)),-6);
    }
    /**
     * @Route("/conference/registration", name="conference_registration")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
//        $form = $this->createForm(MyFrmType::class,null,['attr'=>['class'=>'row']]);
        $form = $this->createForm(ConferenceOrganizationFormType::class);

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
                $conferenceMember->setConference($ConferenceOrganization->getConference());
                $user = $conferenceMember->getUser();
                /** @var User $oldUser */
                $oldUser = $em->getRepository(User::class)
                    ->findOneBy(['email' =>$user->getEmail() ]);
                dump('oldUser',$oldUser,'user', $user);

                $user->setId($oldUser->getId());
                $user->setFirstName('DUPLICATE');
                $user->setOrganization($ConferenceOrganization->getOrganization());
                $em->persist($ConferenceOrganization->getOrganization());
                $password = $this->getRandomPassword();
                dump($password );
                $user->setPassword(
                    $passwordEncoder->encodePassword( $user, $password)
                );

                $em->merge($user);
///                $em->persist($user);
                $em->flush();
//                $user->
                dump('oldUser',$oldUser,'user', $user);

            }
//            dd($ConferenceOrganization);
            $em->persist($ConferenceOrganization);

//            dd($ConferenceOrganization);
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

