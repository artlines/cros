<?php

namespace App\Controller;

use App\Entity\Abode\RoomType;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Form\ConferenceOrganizationFormType;
use App\Form\OrganizationFormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConferenceRegistrationController extends AbstractController
{
    const DIRECTORY_UPLOAD = 'uploads/';

    private function getRandomPassword()
    {
        return substr( md5(random_bytes(10)),-6);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
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

//        $data = $request->get('conference_organization_form');
//        foreach ($data['ConferenceMembers'] as $key => $cm ){
//            if( $cm['neighbourhood']=='' ){
//                $data['ConferenceMembers'][$key]['neighbourhood'] = null;
//            }
//            //dd();
//        }
//        $request->request->set('conference_organization_form', $data);
////            neighbourhood
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

        $em  = $this->getDoctrine()->getManager();


        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ConferenceOrganization $ConferenceOrganization */
            $ConferenceOrganization = $form->getData();
            $em->getConnection()->beginTransaction();

            $organization = $ConferenceOrganization->getOrganization();

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

                $user->setOrganization($organization);


                $em->persist($organization);


                $conference = $ConferenceOrganization->getConference();
                $conferenceMember->setConference($conference);
                $conferenceMember->setConferenceOrganization($ConferenceOrganization);
                $conferenceMember->setNeighbourhood(null);
                $em->persist($ConferenceOrganization); // !!DUP
                $em->flush();
            }
            // TODO: get duplicate  Organization
//            $em->persist($ConferenceOrganization->getOrganization());




            $files = $request->files->get('conference_organization_form');
            if($files and isset(
                    $files['organization'],
                    $files['organization']['newlogo']
                )) {


                /** @var UploadedFile $file */
                $file = $files['organization']['newlogo'];
//                $file = $organization->getLogo();

//                dd($tmp);
//                $file = new UploadedFile($tmp['path'],$tmp['ori'],);
//                move_uploaded_file()



            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
            // Move the file to the directory where brochures are stored
            try {
                $r =
                $file->move(
                    self::DIRECTORY_UPLOAD.'logos/',
                    $fileName
                );
                dd($r);
            } catch (FileException $e) {
                dd($e);
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $organization->setLogo($fileName);

            }

            $em->flush();

            $em->getConnection()->commit();


            return $this->render('conference_registration/registration_success.html.twig', [
                'ConferenceOrganization' => $ConferenceOrganization,
            ]);
        }
        $roomTypes = $em
            ->getRepository(RoomType::class)
            ->findAll();


        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
            'RoomTypes' => $roomTypes,
            'Conference' => $Conference,
            'LimitUsersByOrg' => $Conference->getLimitUsersByOrg(),
            'LimitUsersGlobal' => $Conference->getLimitUsersGlobal(),
            'Users'   => 0, // TODD: get real users value
        ]);
    }
}

