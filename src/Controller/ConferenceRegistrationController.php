<?php

namespace App\Controller;

use App\Entity\Abode\RoomType;
use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Form\ConferenceOrganizationFormType;
use App\Form\OrganizationFormType;
use App\Repository\ConferenceMemberRepository;
use App\Repository\ConferenceOrganizationRepository;
use App\Service\Mailer;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConferenceRegistrationController extends AbstractController
{
    const DIRECTORY_UPLOAD = 'uploads/';
    const MAIL_SEND_CODE   = 'cros.send.code';
    const MAIL_SEND_REGISTERED   = 'cros.send.registered';
    const MAIL_SEND_PASSWORD     = 'cros.send.password';
    const MAIL_BCC               = 'cros@nag.ru';

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
     * @Route("/conference/registration-validate")
     */

    public function validateInn(Request $request){
        $inn = $request->get('inn');
        $kpp = $request->get('kpp');
        $conf_id = $request->get('conf_id');

        $repository = $this
            ->getDoctrine()
            ->getRepository(ConferenceOrganization::class);
        /** @var ConferenceOrganization $value */

        /** @var ConferenceOrganizationRepository $repository */
        //$co = $repository->findByInnKppIsFinish('4502013089', '450201001', 3);
        $co = $repository->findByInnKppIsFinish($inn, $kpp, $conf_id);
        if($co){
            return new JsonResponse(['found'=>$co->getOrganization()->getName()]);
        }
        return new JsonResponse(['found'=>false]);
    }

    /**
     * @Route("/conference/registration-validate-email")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function validateEmail(Request $request) {
/*
 * Если есть дубли с текущей конференции - сообщать от дублировании
 * и не давать завершать регистрацию. Проверку проводить асинхронно
 * - сразу после ввода e-mail (onBlur).
 */
        $email = $request->get('email');
        $conf_id = $request->get('conf_id');

        $repository = $this
            ->getDoctrine()
            ->getRepository(ConferenceMember::class);
        /** @var ConferenceOrganization $value */

        /** @var ConferenceMemberRepository $repository */
        //$co = $repository->findByInnKppIsFinish('4502013089', '450201001', 3);
        $cm = $repository->findConferenceMemberByEmail($conf_id,$email);
        if($cm){
            return new JsonResponse(['found'=>$cm->getUser()->getId()]);
        }
        return new JsonResponse(['found'=>false]);
    }

    public function generateCode($email){
        $c = substr(md5($email),-4);
        return substr(md5($c),-4) . $c;
    }

    /**
     * @Route("/conference/registration-validate-code")
     */
    public function validateCode(Request $request){
        $code = $request->get('code');

        if($code
            and strlen($code)==8
            and substr($code,0,4) == substr(md5(substr($code,-4)),-4)
        ){
            // substr($code,4,4)==md5(substr($code,0,4))
            return new JsonResponse(['found'=>true]);
        }
        return new JsonResponse(['found'=>false]);
    }

    /**
     * @Route("/conference/registration-email-code")
     */

    public function validateEmailSend(Request $request, Mailer $mailer){
        $email = $request->get('email');

        if($email and filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mailer->setTemplateAlias(self::MAIL_SEND_CODE);
            $mailer->send(
                'КРОС: Код подтверждения',
                [
                    'email' => $email,
                    'code'  => $this->generateCode($email)
                ],$email );
            return new JsonResponse(['found'=>true]);
        }
        return new JsonResponse(['found'=>false]);
    }

    /**
     * @Route("/conference/registration-mail/{hash}")
     */
    public function mail_dev(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Mailer $mailer
    )
    {
        $hash = $request->get('hash');
        if ($hash) {
            /** @var ConferenceOrganization $ConferenceOrganization */
            $ConferenceOrganization = $this->getDoctrine()
                ->getRepository(ConferenceOrganization::class)
                ->findOneBy(['inviteHash' => $hash]);
            $arUsers = [];
            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
                $arUsers[] = [
                    'firstName' => $conferenceMember->getUser()->getFirstName(),
                    'middleName' => $conferenceMember->getUser()->getMiddleName(),
                    'lastName' => $conferenceMember->getUser()->getLastName(),
                    'post' => $conferenceMember->getUser()->getPost(),
                    'phone' => $conferenceMember->getUser()->getPhone(),
                    'email' => $conferenceMember->getUser()->getEmail(),
                    'carNumber' => $conferenceMember->getCarNumber(),
                    'roomType' => $conferenceMember->getRoomType()->getTitle(),
                    'cost' => $conferenceMember->getRoomType()->getCost(),
                    'arrival' => $conferenceMember->getArrival()->getTimestamp(),
                    'leaving' => $conferenceMember->getLeaving()->getTimestamp(),
                ];
            }
            $params_organization = [
                'name' => $ConferenceOrganization->getOrganization()->getName(),
                'inn' => $ConferenceOrganization->getOrganization()->getInn(),
                'kpp' => $ConferenceOrganization->getOrganization()->getKpp(),
                'requisites' => $ConferenceOrganization->getOrganization()->getRequisites(),
                'address' => $ConferenceOrganization->getOrganization()->getAddress(),
                'comment' => $ConferenceOrganization->getOrganization()->getComment(),
                'users' => $arUsers
            ];

            $mailer->setTemplateAlias(self::MAIL_SEND_REGISTERED);
            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
//            die();
                dump($params_organization);
                return $this->render('conference_registration/mail_cros_send_reg.twig', [
                    'data' => [
                        'organization' => $params_organization,
                        'conference' => [
                            'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                            'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                        ]
                ],
            ]);
            dump($mailer->send(
                'КРОС. Регистрация завершена',
                [
                    'organization' => $params_organization
                ],
                $conferenceMember->getUser()->getEmail()
            ));

            }
        }
        return new Response('OK');
    }


    /**
     * @Route("/conference/registration/{hash}", name="conference_registration_hash")
     * @Route("/conference/registration", name="conference_registration")
     */
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Mailer $mailer
    )
    {
//        $mailer->setClientAlias('cros.send.validation-ccde');
//        $mailer->setClientAlias('cros.send.user');
//        $mailer->setClientAlias('cros.send.registration');
        $hash = $request->get('hash');
        //$ConferenceOrganization = new ConferenceOrganization();
        if( $hash ) {
            $ConferenceOrganization = $this->getDoctrine()
                ->getRepository(ConferenceOrganization::class)
                ->findOneBy(['inviteHash'=>$hash]);
            $form = $this->createForm(
                ConferenceOrganizationFormType::class, $ConferenceOrganization);
            if ( !$ConferenceOrganization) {
                throw $this->createNotFoundException();
            }
            if ($ConferenceOrganization->isFinish()) {
                return  $this->render('conference_registration/registration_success.html.twig', [
                    'ConferenceOrganization' => $ConferenceOrganization,
                    'UserPasswords' => [],
                    'ended' => 1,
                ]);
            }
        } else {
            $form = $this->createForm(
                ConferenceOrganizationFormType::class);
        }
//        $arUsers = [];
//        foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
//            $arUsers[] = [
//                'firstName' => $conferenceMember->getUser()->getFirstName(),
//                'middleName' => $conferenceMember->getUser()->getMiddleName(),
//                'lastName' => $conferenceMember->getUser()->getLastName(),
//                'post' => $conferenceMember->getUser()->getPost(),
//                'phone' => $conferenceMember->getUser()->getPhone(),
//                'email' => $conferenceMember->getUser()->getEmail(),
//                'carNumber' => $conferenceMember->getCarNumber(),
//                'roomType'  => $conferenceMember->getRoomType()->getTitle(),
//                'cost' => $conferenceMember->getRoomType()->getCost(),
//                'arrival' => $conferenceMember->getArrival()->getTimestamp(),
//                'leaving' => $conferenceMember->getLeaving()->getTimestamp(),
//            ];
//        }
//        $params_organization =  [
//            'name' => $ConferenceOrganization->getOrganization()->getName(),
//            'inn' => $ConferenceOrganization->getOrganization()->getInn(),
//            'kpp' => $ConferenceOrganization->getOrganization()->getKpp(),
//            'requisites' => $ConferenceOrganization->getOrganization()->getRequisites(),
//            'address' => $ConferenceOrganization->getOrganization()->getAddress(),
//            'comment' => $ConferenceOrganization->getOrganization()->getComment(),
//            'users' => $arUsers
//        ];
//
//        $mailer->setTemplateAlias(self::MAIL_SEND_REGISTERED);
//        foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
//            dump($params_organization);
//            dd( $mailer->send(
//                'КРОС. Регистрация завершена',
//                [
//                    'organization' => $params_organization
//                ],
//                $conferenceMember->getUser()->getEmail()
//            ));
//        }
//dd('stop');

//        $form = $this->createForm(MyFrmType::class,null,['attr'=>['class'=>'row']]);
        $arUserPassword = [];
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
//            dd($ConferenceOrganization);
            $em->getConnection()->beginTransaction();
/*
            foreach ($ConferenceOrganization->getConferenceMembers() as $user_num => $conferenceMember ) {
                $conferenceMember->setNeighbourhood(null);
                $oldUser = $em->getRepository(User::class)
                    ->findOneBy(['email' =>'esuzev@gmail.com' ]);
                $conferenceMember->setUser($oldUser);
                $conferenceMember->getUser()->setOrganization($ConferenceOrganization->getOrganization());
                $conferenceMember->getUser()->setPassword('123');
                $conferenceMember->setConference($ConferenceOrganization->getConference());
            }

            $em->persist($ConferenceOrganization);
            $em->flush();*/



//            dd($ConferenceOrganization);
            $organization = $ConferenceOrganization->getOrganization();
            $files = $request->files->get('conference_organization_form');

            //setup Conference for conferenceMembers
            foreach ($ConferenceOrganization->getConferenceMembers() as $user_num => $conferenceMember ) {
                $user = $conferenceMember->getUser();
                $conferenceMember->setNeighbourhood(
                    ($conferenceMember->getNeighbourhood() !== null)
                    ? $ConferenceOrganization->getConferenceMembers()->get($conferenceMember->getNeighbourhood())
                    : null
                );
                /** @var User $oldUser */
                $oldUser = $em->getRepository(User::class)
                    ->findOneBy(['email' =>$user->getEmail() ]);
                if( $oldUser ) {
                    $conferenceMember->setUser($oldUser);
                }
                $password = $this->getRandomPassword();
                $user->setPassword(
                    $passwordEncoder->encodePassword( $user, $password)
                );
                $arUserPassword[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'password' => $password,
                ];

                $user->setOrganization($organization);

//                $conferenceMember->setUser($user);
//
                // conference_organization_form[ConferenceMembers][0][user][newphoto]
                if($files and isset(
                        $files['ConferenceMembers'],
                        $files['ConferenceMembers'][$user_num],
                        $files['ConferenceMembers'][$user_num]['user'],
                        $files['ConferenceMembers'][$user_num]['user']['newphoto']
                    )) {

                    /** @var UploadedFile $file */
                    $file = $files['ConferenceMembers'][$user_num]['user']['newphoto'];
//                    $fileName = $user->getId().'.'.$file->guessExtension();
                    $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            self::DIRECTORY_UPLOAD.'/members/users/',
                            $fileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $user->setPhoto($fileName);
                }



                $conference = $ConferenceOrganization->getConference();
                $conferenceMember->setConference($conference);
                $conferenceMember->setConferenceOrganization($ConferenceOrganization);
//                $conferenceMember->setNeighbourhood(null);
////                $em->flush();
//                $conferenceMember->setNeighbourhood(new ConferenceMember());
//                $em->persist($conferenceMember);
            }
//            $em->persist($organization);
//
            $em->persist($ConferenceOrganization); // !!DUP
////            dd($ConferenceOrganization);
//            // TODO: get duplicate  Organization
////            $em->persist($ConferenceOrganization->getOrganization());
//            dump($ConferenceOrganization);
//            $em->flush();
//            dump($conferenceMember);
//
//
//
//
            if($files and isset(
                    $files['organization'],
                    $files['organization']['newlogo']
            )) {
                /** @var UploadedFile $file */
                $file = $files['organization']['newlogo'];
//                $fileName = $organization->getId().'.'.$file->guessExtension();
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        self::DIRECTORY_UPLOAD.'members/logos/',
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $organization->setLogo($fileName);
            }

// TODO: enable finish

            $ConferenceOrganization->setFinish(true);
            dump($ConferenceOrganization);
            dump('UserPassword',$arUserPassword);
            $em->flush();

            $em->getConnection()->commit();

            $arUsers = [];
            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
                $arUsers[] = [
                    'firstName' => $conferenceMember->getUser()->getFirstName(),
                    'middleName' => $conferenceMember->getUser()->getMiddleName(),
                    'lastName' => $conferenceMember->getUser()->getLastName(),
                    'post' => $conferenceMember->getUser()->getPost(),
                    'phone' => $conferenceMember->getUser()->getPhone(),
                    'email' => $conferenceMember->getUser()->getEmail(),
                    'carNumber' => $conferenceMember->getCarNumber(),
                    'roomType'  => $conferenceMember->getRoomType()->getTitle(),
                    'cost' => $conferenceMember->getRoomType()->getCost(),
                    'arrival' => $conferenceMember->getArrival()->getTimestamp(),
                    'leaving' => $conferenceMember->getLeaving()->getTimestamp(),
                ];
            }
            $params_organization =  [
                'name' => $ConferenceOrganization->getOrganization()->getName(),
                'inn' => $ConferenceOrganization->getOrganization()->getInn(),
                'kpp' => $ConferenceOrganization->getOrganization()->getKpp(),
                'requisites' => $ConferenceOrganization->getOrganization()->getRequisites(),
                'address' => $ConferenceOrganization->getOrganization()->getAddress(),
                'comment' => $ConferenceOrganization->getOrganization()->getComment(),
                'users' => $arUsers
            ];

            $mailer->setTemplateAlias(self::MAIL_SEND_REGISTERED);
            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
                if( $conferenceMember->getUser()->isRepresentative()) {
                    dump($params_organization);
                    dump($mailer->send(
                        'КРОС. Регистрация завершена',
                        [
                            'organization' => $params_organization,
                            'conference' => [
                                'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                                'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                            ]
                        ],
                        $conferenceMember->getUser()->getEmail(), null, self::MAIL_BCC
                    ));
                    dump([
                        'organization' => $params_organization,
                        'conference' => [
                            'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                            'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                        ]
                    ]);
                }

            }
            $mailer->setTemplateAlias(self::MAIL_SEND_PASSWORD );

            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
                dump($conferenceMember->getUser()->getEmail());
                $item = false;
                foreach ($arUserPassword as $k => $item_look){
                    dump($item_look);
                    if ($item_look['email']==$conferenceMember->getUser()->getEmail() ) {
                        $item = $arUserPassword[$k];
                        dump('$arUserPassword', $item);
                    }
                }
                dump($arUserPassword,$item);

                $user = [
                    'firstName' => $conferenceMember->getUser()->getFirstName(),
                    'middleName' => $conferenceMember->getUser()->getMiddleName(),
                    'lastName' => $conferenceMember->getUser()->getLastName(),
                    'post' => $conferenceMember->getUser()->getPost(),
                    'phone' => $conferenceMember->getUser()->getPhone(),
                    'email' => $conferenceMember->getUser()->getEmail(),
                    'carNumber' => $conferenceMember->getCarNumber(),
                    'roomType'  => $conferenceMember->getRoomType()->getTitle(),
                    'cost' => $conferenceMember->getRoomType()->getCost(),
                    'arrival' => $conferenceMember->getArrival()->getTimestamp(),
                    'leaving' => $conferenceMember->getLeaving()->getTimestamp(),
                ];

                dump($mailer->send(
                    'КРОС. Пароль для доступа',
                    [
                        'user'     => $user,
                        'email'    => $item['email'],
                        'password' => $item['password'],
                        'organization' => $params_organization,
                        'conference' => [
                            'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                            'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                        ]
                    ],
                    $conferenceMember->getUser()->getEmail() ,null, self::MAIL_BCC
                ));
            }
//            representative



            return $this->render('conference_registration/registration_success.html.twig', [
                'ConferenceOrganization' => $ConferenceOrganization,
                'UserPasswords' => $arUserPassword,
            ]);

        }
        $roomTypes = $em
            ->getRepository(RoomType::class)
            ->findAllFreeForConference($Conference->getId());
//dd($roomTypes);
        $TotalFree = 0;
        $TotalUsed = 0;
        foreach ($roomTypes as list($RoomType, $used)){
            /** @var RoomType $RoomType */
            $TotalFree += max(0, $RoomType->getMaxPlaces()-$used);
            $TotalUsed += $used;
        }

        if($TotalFree<1 or $TotalUsed>=$Conference->getLimitUsersGlobal() ){
            return $this->render(
                'conference_registration/completed.html.twig',
                []
            );
        }
        return $this->render('conference_registration/index.html.twig', [
//            'controller_name' => 'ConferenceRegistrationController',
            'form' => $form->createView(),
            'ConferenceOrganization' => $ConferenceOrganization ?? null,
            'RoomTypes' => $roomTypes,
            'Conference' => $Conference,
            'LimitUsersByOrg' => $Conference->getLimitUsersByOrg(),
            'LimitUsersGlobal' => $Conference->getLimitUsersGlobal(),
            'Users'   => 0, // TODD: get real users value
        ]);
    }
}

