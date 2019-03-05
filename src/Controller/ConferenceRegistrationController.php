<?php

namespace App\Controller;

use App\Entity\Abode\RoomType;
use App\Entity\Conference;
use App\Entity\Participating\Comment;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Entity\Participating\Organization;
use App\Entity\Participating\User;
use App\Form\CommentFormType;
use App\Form\ConferenceMemberFormType;
use App\Form\ConferenceOrganizationFormType;
use App\Repository\Abode\RoomTypeRepository;
use App\Repository\ConferenceMemberRepository;
use App\Repository\ConferenceOrganizationRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class ConferenceRegistrationController extends AbstractController
{

    const DIRECTORY_UPLOAD = 'uploads/';
    const MAIL_SEND_CODE = 'cros.send.code';
    const MAIL_SEND_REGISTERED = 'cros.send.registered';
    const MAIL_SEND_PASSWORD = 'cros.send.password';
    const MAIL_SEND_COMMENT = 'cros.send.comment';
    const MAIL_BCC = 'cros@nag.ru';

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    private function getRandomPassword()
    {
        return substr(md5(random_bytes(10)), -6);
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

    private function getBcc()
    {
        return getenv('CROS_MAIL_BCC');
    }


    /**
     * @Route("/conference/registration-validate")
     */

    public function validateInn(Request $request)
    {
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
        if ($co) {
            return new JsonResponse(['found' => $co->getOrganization()->getName()]);
        }
        return new JsonResponse(['found' => false]);
    }

    /**
     * @Route("/conference/registration-validate-email")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */

    public function validateEmail(Request $request)
    {
        /*
         * Если есть дубли с текущей конференции - сообщать от дублировании
         * и не давать завершать регистрацию. Проверку проводить асинхронно
         * - сразу после ввода e-mail (onBlur).
         */
        $email = $request->get('email');
        $conf_id = $request->get('conf_id');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['found' => 'email-invalid']);
        }

        $repository = $this
            ->getDoctrine()
            ->getRepository(ConferenceMember::class);
        /** @var ConferenceOrganization $value */

        /** @var ConferenceMemberRepository $repository */
        //$co = $repository->findByInnKppIsFinish('4502013089', '450201001', 3);
        $cm = $repository->findConferenceMemberByEmail($conf_id, $email);
        if ($cm) {
            return new JsonResponse(['found' => $cm->getUser()->getId()]);
        }
        return new JsonResponse(['found' => false]);
    }

    public function generateCode($email)
    {
        $c = substr(md5($email), -4);
        return substr(md5($c), -4) . $c;
    }

    /**
     * @Route("/conference/registration-validate-code")
     */
    public function validateCode(Request $request)
    {
        $code = $request->get('code');

        if ($code
            and strlen($code) == 8
            and substr($code, 0, 4) == substr(md5(substr($code, -4)), -4)
        ) {
            // substr($code,4,4)==md5(substr($code,0,4))
            return new JsonResponse(['found' => true]);
        }
        return new JsonResponse(['found' => false]);
    }

    /**
     * @Route("/conference/registration-email-code")
     */

    public function validateEmailSend(Request $request, Mailer $mailer)
    {
        $email = $request->get('email');

        if ($email and filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mailer->setTemplateAlias(self::MAIL_SEND_CODE);
            $mailer->send(
                'КРОС 2019: Код подтверждения',
                [
                    'email' => $email,
                    'code' => $this->generateCode($email)
                ], $email);
            return new JsonResponse(['found' => true]);
        }
        return new JsonResponse(['found' => false]);
    }


    /**
     * @Route("/registration/{hash}", name="conference_registration_hash")
     * @Route("/registration", name="registration")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Mailer $mailer
    )
    {
        if ($request->getMethod() == 'POST') {
            $cloner = new VarCloner();
            $dumper = new CliDumper();
            $output = fopen('php://memory', 'r+b');

            $dumper->dump($cloner->cloneVar($request), $output);
            $output = stream_get_contents($output, -1, 0);
            $this->logger->notice('POSTDATA:' . base64_encode($output));
        }
        $hash = $request->get('hash');
        if ($hash) {
            $ConferenceOrganization = $this->getDoctrine()
                ->getRepository(ConferenceOrganization::class)
                ->findOneBy(['inviteHash' => $hash]);
            $form = $this->createForm(
                ConferenceOrganizationFormType::class, $ConferenceOrganization);
            if (!$ConferenceOrganization) {
                throw $this->createNotFoundException();
            }
            if ($ConferenceOrganization->isFinish()) {
                return $this->render('conference_registration/registration_success.html.twig', [
                    'ConferenceOrganization' => $ConferenceOrganization,
                    'UserPasswords' => [],
                    'ended' => 1,
                ]);
            }
            $Conference = $ConferenceOrganization->getConference();
        } else {
            /** @var Conference $Conference */
            $Conference = $this->getDoctrine()->getRepository(Conference::class)
                ->findOneBy(['year' => date("Y")]);

            // Получаем разрешенные даты регистрации
            $reg_start = $Conference->getRegistrationStart();
            $reg_finish = $Conference->getRegistrationFinish();

            $now = date('Y-m-d H:i:s');
            // Закрыта
            if (!($reg_start->format('Y-m-d H:i:s') <= $now && $reg_finish->format('Y-m-d H:i:s') >= $now)) {

                return $this->render('conference_registration/registration_closed.html.twig',
                    ['conf' => $Conference]
                );
            }
            $form = $this->createForm(
                ConferenceOrganizationFormType::class);
        }

        /** @var RoomTypeRepository $roomTypeRepo */
        $roomTypeRepo = $this->getDoctrine()->getRepository(RoomType::class);
        $roomTypesInfo = $roomTypeRepo->getSummaryInformation();
        $TotalFree = 0;
        $TotalUsed = 0;

        foreach ($roomTypesInfo as $type) {
            /** @var RoomType $RoomType */
            $TotalFree += max(0, $type['total'] - $type['busy'] - $type['reserved']);
            $TotalUsed += $type['busy'] + $type['reserved'];
        }

        if ($TotalFree < 1 or $TotalUsed >= $Conference->getLimitUsersGlobal()) {
            return $this->render('conference_registration/registration_closed.html.twig',
                ['conf' => $Conference]
            );
        }

        $arUserPassword = [];

        $form->handleRequest($request);
        /** @var ConferenceOrganization $ConferenceOrganization */

        $em = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var ConferenceOrganization $ConferenceOrganization */
            $ConferenceOrganization = $form->getData();
            $em->getConnection()->beginTransaction();

            $organization = $ConferenceOrganization->getOrganization();
            $files = $request->files->get('conference_organization_form');

            //setup Conference for conferenceMembers
            foreach ($ConferenceOrganization->getConferenceMembers() as $user_num => $conferenceMember) {
                $user = $conferenceMember->getUser();
                $conferenceMember->setNeighbourhood(
                    ($conferenceMember->getNeighbourhood() !== null)
                        ? $ConferenceOrganization->getConferenceMembers()->get($conferenceMember->getNeighbourhood())
                        : null
                );
                /** @var User $oldUser */
                $oldUser = $em->getRepository(User::class)
                    ->findOneBy(['email' => $user->getEmail()]);
                if ($oldUser) {
                    $oldUser->setOrganization($user->getOrganization());
                    $oldUser->setFirstName($user->getFirstName());
                    $oldUser->setLastName($user->getLastName());
                    $oldUser->setMiddleName($user->getMiddleName());
                    $oldUser->setSex($user->getSex());
                    $oldUser->setPost($user->getPost());
                    $oldUser->setPhone($user->getPhone());
                    $oldUser->setRepresentative($user->isRepresentative());

                    $conferenceMember->setUser($oldUser);
                    $user = $oldUser;
                }
                $user->setPhone(preg_replace('/[\D]/', '', $user->getPhone()));
                $password = $this->getRandomPassword();
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $password)
                );
                $arUserPassword[] = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'password' => $password,
                ];

                $user->setOrganization($organization);

                if ($files and isset(
                        $files['ConferenceMembers'],
                        $files['ConferenceMembers'][$user_num],
                        $files['ConferenceMembers'][$user_num]['user'],
                        $files['ConferenceMembers'][$user_num]['user']['newphoto']
                    )) {

                    /** @var UploadedFile $file */
                    $file = $files['ConferenceMembers'][$user_num]['user']['newphoto'];
//                    $fileName = $user->getId().'.'.$file->guessExtension();
                    $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            self::DIRECTORY_UPLOAD . '/members/users/',
                            $fileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $user->setPhoto($fileName);
                }


                $conference = $ConferenceOrganization->getConference();
                if (is_null($conferenceMember->getArrival())) {
                    $conferenceMember->setArrival($Conference->getEventStart());
                }
                if (is_null($conferenceMember->getLeaving())) {
                    $conferenceMember->setLeaving($Conference->getEventFinish());
                }
                $conferenceMember->setConference($conference);
                $conferenceMember->setConferenceOrganization($ConferenceOrganization);
            }

            $em->persist($ConferenceOrganization); // !!DUP

            if ($files and isset(
                    $files['organization'],
                    $files['organization']['newlogo']
                )) {
                /** @var UploadedFile $file */
                $file = $files['organization']['newlogo'];
//                $fileName = $organization->getId().'.'.$file->guessExtension();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        self::DIRECTORY_UPLOAD . 'members/logos/',
                        $fileName
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $organization->setLogo($fileName);
            }

            $ConferenceOrganization->setFinish(true);
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
                if ($conferenceMember->getUser()->isRepresentative()) {
                    $comment = new Comment();
                    $comment->setConferenceOrganization($ConferenceOrganization);
                    $comment->setUser($conferenceMember->getUser());
                    // NOT DO $comment->setContent($ConferenceOrganization->getNotes());
                    $content = $request->get('conference_organization_form')['notes'];
                    $comment->setContent($content);
                    if ($content) {
                        $em->persist($comment);
                    }
                    $mailer->send(
                        'КРОС 2019: ' . $ConferenceOrganization->getOrganization()->getName(),
                        [
                            'organization' => $params_organization,
                            'conference' => [
                                'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                                'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                            ]
                        ],
                        $conferenceMember->getUser()->getEmail(), null, $this->getBcc()
                    );
                }

            }
            $mailer->setTemplateAlias(self::MAIL_SEND_PASSWORD);

            foreach ($ConferenceOrganization->getConferenceMembers() as $conferenceMember) {
                $item = false;
                foreach ($arUserPassword as $k => $item_look) {
                    if ($item_look['email'] == $conferenceMember->getUser()->getEmail()) {
                        $item = $arUserPassword[$k];
                    }
                }

                $user = [
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

                $mailer->send(
                    'КРОС 2019: Пароль для доступа',
                    [
                        'user' => $user,
                        'email' => $item['email'],
                        'password' => $item['password'],
                        'organization' => $params_organization,
                        'conference' => [
                            'eventStart' => $ConferenceOrganization->getConference()->getEventStart()->getTimestamp(),
                            'eventFinish' => $ConferenceOrganization->getConference()->getEventFinish()->getTimestamp(),
                        ]
                    ],
                    $conferenceMember->getUser()->getEmail(), null, $this->getBcc()
                );
            }
            $em->flush();

            return $this->render('conference_registration/registration_success.html.twig', [
                'ConferenceOrganization' => $ConferenceOrganization,
                'UserPasswords' => $arUserPassword,
            ]);

        }

        return $this->render('conference_registration/index.html.twig', [
            'form' => $form->createView(),
            'ConferenceOrganization' => $ConferenceOrganization ?? null,
            'roomTypesInfo' => $roomTypesInfo,
            'Conference' => $Conference,
            'LimitUsersByOrg' => $Conference->getLimitUsersByOrg(),
            'LimitUsersGlobal' => $Conference->getLimitUsersGlobal(),
            'Users' => 0, // TODD: get real users value
        ]);
    }


    /**
     * @Route("/registration-show", name="registration_show")
     * @param Request $request
     * @param Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registrationShow(Request $request, Mailer $mailer, UserPasswordEncoderInterface $passwordEncoder)
    {
        /** @var User $user */
        /** @var Organization $organization */
        if (!$this->getUser()) {
            return $this->render('conference_registration/no_access.html.twig');
        }

        if (!$this->getUser()->getOrganization()){
            return $this->render('conference_registration/no_access.html.twig');
        }

        $organization = $this->getUser()->getOrganization();
        $Conference = $this->getDoctrine()
            ->getRepository(Conference::class)
            ->findOneBy(['year' => date("Y")]);

        if ($organization and $Conference) {
            /** @var ConferenceOrganization $conferenceOrganization */
            $conferenceOrganization = $this->getDoctrine()
                ->getRepository(ConferenceOrganization::class)
                ->findOneBy([
                    'organization' => $organization,
                    'conference' => $Conference,
                ]);
            if (!$conferenceOrganization) {
                return $this->render('conference_registration/no_access.html.twig');
            }

            $CommentForm = $this->createForm(
                CommentFormType::class
            )
            ;

            /** @var ConferenceMember $CM */
            $CM = (isset($request->get('conference_member_form')['id']))
                ? $this->getDoctrine()
                    ->getRepository(ConferenceMember::class)
                    ->findOneBy([
                        'id'=> intval($request->get('conference_member_form')['id'])
                    ])
                : null;
            if (!$CM) {
               $CM = new ConferenceMember();
            }
            $CMRoomType = $CM
                ? $CM->getRoomType()
                : null;

            $CM->setConferenceOrganization($conferenceOrganization);
            $CM->setConference($conferenceOrganization->getConference());
            $currentMemberFormViews = [];
            $submitted = -1;
            foreach ($conferenceOrganization->getConferenceMembers() as $key => $iConferenceMember) {
                $form = $this->createForm(
                    ConferenceMemberFormType::class,
                    $CM == $iConferenceMember
                        ? $CM
                        : $iConferenceMember
                )
                    ->remove('RoomType')
                    ->get('user')->remove( 'representative')->getParent()
                    ->add('id', HiddenType::class)
                    ->add(
                        'save',
                        SubmitType::class,
                        [
                            'label' => 'Редактировать участника',
                            'attr' => [
                                'class' => 'u-btn-darkblue cs-font-size-13 cs-px-10 cs-py-10 mb-0 cs-mt-15'
                            ]
                        ]
                    );
                if ($CM == $iConferenceMember){
                    $submitted = $key;
                    $form->handleRequest($request);
                }

                $currentMemberFormViews[$key] = $form
                    ->createView();
            }

            $memberForm = $this->createForm(
                ConferenceMemberFormType::class, $CM)
                ->remove('neighbourhood')
                ->remove('roomType')
                ->add(
                    'save',
                    SubmitType::class,
                    [
                        'label' => 'Добавить участника',
                        'attr' => [
                            'class' => 'u-btn-darkblue cs-font-size-13 cs-px-10 cs-py-10 mb-0 cs-mt-15'
                        ]
                    ]
                );
            $memberForm->remove('roomType');
            if($CM){
                // Если редактируем
                $memberForm->add('id', HiddenType::class);
                $memberForm->remove('roomType');
            }
            ;
            if( $submitted == -1 ) {
                $memberForm->handleRequest($request);
            }
            if ($memberForm->isSubmitted() && $memberForm->isValid()) {
                /** @var EntityManager $em */
                /** @var ConferenceMember $CM */
                $CMNew = $memberForm->getData();
                // restore roomType value ->remove('roomType') not works
                if ($CM && $CMRoomType) {
                    $CMNew->setRoomType($CMRoomType);
                }
                $user = $CMNew->getUser();
                $user->setPhone(preg_replace('/[\D]/', '', $user->getPhone()));
                $user->setOrganization($conferenceOrganization->getOrganization());
                $password = $this->getRandomPassword();
                $user->setPassword(
                    $passwordEncoder->encodePassword($user, $password)
                );
                $CMNew->setConference($conferenceOrganization->getConference());
                $CMNew->setConferenceOrganization($conferenceOrganization);
                $em = $this->getDoctrine()->getManager();
                $em->persist($CMNew);
                $em->flush();
                return $this->redirectToRoute('registration_show');
            }
            $CommentForm->handleRequest($request);
//            if ($request->request->has('conference_member_form')) {
//
//            }

            if ($CommentForm->isSubmitted() && $CommentForm->isValid()) {

                /** @var Comment $Comment */
                $comment = $CommentForm->getData();
                /** @var Comment $comment */
                $comment
                    ->setUser($this->getUser())
                    ->setConferenceOrganization($conferenceOrganization)
                    ;
                /** @var EntityManager $em */
                $em = $this->getDoctrine()->getManager();
                $em->persist($comment);
                $em->flush();
                // send mail
                $mailer->setTemplateAlias(self::MAIL_SEND_COMMENT);
                $mailer->send(
                    'КРОС 2019: ' . $organization->getName(),
                    [
                        'organization' => [
                            'name' => $organization->getName()
                        ],
                        'comment' => $comment->getContent(),
                        'user' => [
                            'firstName' => $comment->getUser()->getFirstName(),
                            'middleName' => $comment->getUser()->getMiddleName(),
                            'lastName' => $comment->getUser()->getLastName(),
                        ]
                    ],
                    $this->getBcc()
//                    $this->getUser()->getEmail(), null, $this->getBcc()
                );

                return $this->redirectToRoute('registration_show');
            }

            $comments = $this->getDoctrine()
                ->getRepository(Comment::class)
                ->findBy([
                    'conferenceOrganization' => $conferenceOrganization,
                    'isPrivate' => false,
                ], ['createdAt' =>'DESC'])
            ;

            return $this->render('conference_registration/show.html.twig', [
                'ConferenceOrganization' => $conferenceOrganization,
                'comments' => $comments,
                'form' => $CommentForm->createView(),
                'memberForm' => $memberForm->createView(),
                'currentMemberFormViews' => $currentMemberFormViews,
                'submitted' => $submitted,
            ]);
        } else {
            throw $this->createNotFoundException();

        }
    }

}

