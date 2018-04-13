<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\Speaker;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use AppBundle\Form\SpeakerType;
use AppBundle\Repository\ConferenceRepository;
use AppBundle\Repository\OrgToConfRepository;
use AppBundle\Repository\SpeakerRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Repository\UserToApartamentRepository;
use AppBundle\Repository\UserToConfRepository;
use function PHPSTORM_META\type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Form\Type\VichFileType;
use AppBundle\Service\FileUploader;
use AppBundle\Service\ResizeImages;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;
class AdminMemberController extends Controller
{
    /**
     * Список зарегистрированных пользователей
     *
     * @Route("/admin/members/{year}/{signed}", name="admin-members")
     * @Route("/admin/members/{year}")
     *
     * @param integer $year
     * @param string $signed
     *
     * @return object
     */
    public function adminUsersAction($year = null, $signed = 'all')
    {
        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        /** @var UserToConf $users */
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:UserToConf')
            ->findBy(array('conferenceId' => $conf->getId()));

        /** @var User $users */
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAllByUsers($users, 0);

        $apartaments_array = array();

        $forfilter = array();

        $apartaments = $this->getDoctrine()
            ->getRepository('AppBundle:Apartament')
            ->findAllWithIds();

        $pairs = array();
        $pairs_places = array();

        /** @var Apartament $apartament */
        foreach ($apartaments as $apartament) {
            $freenum = 0;
            $rfn = 0;
            $partialfree = 0;
            $aitoas = $apartament->getAitoas();
            $apartaments_array[$apartament->getId()] = array(
                'id' => $apartament->getId(),
                'title' => $apartament->getTitle(),
                'count' => count($aitoas),
                'free' => 0,
                'places' => $apartament->getPlaces(),
                'ids' => array(),
                'pair' => $apartament->getPair(),
            );
            $places = count($aitoas) * $apartament->getPlaces();
            /** @var ApartamentId $atoa */
            foreach ($aitoas as $atoa) {
                $noapr = 0;
                $apuser = array();
                /** @var UserToApartament $uta */
                $utas = $atoa->getAtoais();
                /** @var UserToApartament $uta */
                foreach ($utas as $uta) {
                    if ($uta->getApproved() != 1) {
                        $noapr++;
                    }
                }
                if ((count($utas) - $noapr) < $apartament->getPlaces()) {
                    for($i = (count($utas) - $noapr); $i < $apartament->getPlaces(); $i++) {
                        $freenum++;
                    }
                }
                if (count($utas) == 0) {
                    $rfn++;
                }

                if ($signed == 'empty' && count($utas) > 0) {
                    foreach ($utas as $uta) {
                        if ($uta->getApproved() == 1) {
                            $places--;
                        }
                    }

                    continue;
                }

                foreach ($utas as $uta) {
                    if ($uta->getApproved() == 1) {
                        $apuser[] = $uta->getUser();
                        $places--;
                    }
                }

                $apartaments_array[$apartament->getId()]['ids'][$atoa->getId()] = array(
                    'id' => $atoa->getId(),
                    'users' => $apuser,
                );
            }

            $free = $rfn;

            if (isset($pairs[$apartament->getPair()])) {
                $pairs[$apartament->getPair()] = $pairs[$apartament->getPair()] - ($apartaments_array[$apartament->getId()]['count'] - $free);
            } else {
                $pairs[$apartament->getPair()] = $free;
            }

            $apartaments_array[$apartament->getId()]['free_spaces'] = $freenum;

            if (isset($pairs_places[$apartament->getPair()])) {
                $pairs_places[$apartament->getPair()] = $pairs_places[$apartament->getPair()] - ($apartaments_array[$apartament->getId()]['free_spaces'] - $places);
            } else {
                $pairs_places[$apartament->getPair()] = $places;
            }
            $apartaments_array[$apartament->getId()]['free'] = $free;
        }

        return $this->render('admin/members/byapart.html.twig', array(
            'users' => $users,
            'apartaments' => $apartaments_array,
            'confid' => $conf->getId(),
            'pairs' => $pairs,
            'pairplaces' => $pairs_places,
            'signed' => $signed,
            'year' => $year,
        ));
    }

    /**
     * Сохранение пользователей по номерам
     *
     * @Route("/admin/members-save", name="save_user_to_apart")
     *
     * @param Request $request
     * @return object
     */
    public function saveMembersAction(Request $request)
    {
        $save = $request->get('save');
        $em = $this->getDoctrine()->getManager();

        if (isset($save['up'])) {
            foreach ($save['up'] as $id => $up) {
                /** @var UserToApartament $user */
                $user = $this->getDoctrine()
                    ->getRepository('AppBundle:UserToApartament')
                    ->findOneBy(array('userId' => $id, 'apartamentsId' => $up));
                if ($user != null) {
                    $user->setApproved(1);

                    $em->persist($user);
                    $em->flush();
                }
            }
        }
        if (isset($save['rm'])) {
            foreach ($save['rm'] as $id => $rm) {
                /** @var UserToApartament $user */
                $user = $this->getDoctrine()
                    ->getRepository('AppBundle:UserToApartament')
                    ->findOneBy(array('userId' => $id, 'apartamentsId' => $rm));

                if ($user) {
                    $user->setApproved(0);

                    $em->persist($user);
                    $em->flush();
                }
            }
        }
        if (isset($save['ch'])) {
            foreach ($save['ch'] as $id => $ch) {
                /** @var UserToApartament $user */
                $user = $this->getDoctrine()
                    ->getRepository('AppBundle:UserToApartament')
                    ->findOneBy(array('userId' => $id, 'apartamentsId' => $ch['old_id']));

                if (!$user) {
                    $ru = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->find($id);
                    if ($ru) {
                        $user = new UserToApartament();
                        $user->setUser($ru);
                    }
                }
                if ($user) {
                    $user->setApproved(1);

                    $apr = $this->getDoctrine()
                        ->getRepository('AppBundle:ApartamentId')
                        ->find($ch['new_id']);
                    if ($apr) {
                        $user->setApartament($apr);

                        $em->persist($user);
                        $em->flush();
                    }
                }
            }
        }

        $response = new JsonResponse('ok');
        return $response;
    }

    /**
     * Менеджеры
     *
     * @Route("/admin/managers", name="admin-managers")
     *
     * @param Request $request
     * @return object
     */
    public function managersAction(Request $request)
    {
        $year = date("Y");

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        $managers = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findManagers($conf->getId(), $year);

        return $this->render('admin/members/managers.html.twig', array(
            'users' => $managers,
        ));
    }

    /**
     * Докладчики
     *
     * @Route("/admin/speakers", name="admin-speakers")
     *
     * @param Request $request
     * @return object
     */
    public function speakersAction(Request $request){
        $year = date("Y");

        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $this->getDoctrine()->getRepository('AppBundle:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(array('year' => $year));

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        /** @var Speaker $speakers */
        $speakers = $speakerRepository->findByConf($conf->getId());

        return $this->render('admin/speakers/list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'list' => $speakers,
        ));
    }

    /**
     * Поиск пользователя
     *
     * @Route("/admin/speaker/find", name="admin-speaker-find")
     */
    public function speakerFindAction(Request $request){
        $find_str = $request->get('find');
        $find_arr = explode(' ', $find_str);

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');

        $uar = array();
        /** @var User $users */
        $users = $userRepository->findUser($find_arr);
        /** @var User $user */
        foreach ($users as $user) {
            $uar[$user->getId()] = array(
                'id' => $user->getId(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'middlename' => $user->getMiddleName() != null ? $user->getMiddleName() : '',
                'org' => $user->getOrganization()->getName(),
            );
        }

        $response = new JsonResponse(json_encode($uar));
        return $response;
    }

    /**
     * Добавление докладчиков
     *
     * @Route("/admin/speaker/new", name="admin-speaker-new")
     */
    public function speakerNewAction(Request $request){


        $em = $this->getDoctrine()->getManager();
        $add_id = $request->get('add_id');
        $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $userRepository->find($add_id);
        if($user) {
            $speaker = new Speaker();
            $speaker->setUser($user);
            $speaker->setPublish(1);
            $speaker->setConferenceId(12);
            $em->persist($speaker);
            $em->flush();

            //return new Response($speaker->getId());
            var_dump(new Response($speaker->getId()));
            echo "Вошли в добавление";
            die();
        }
        else{
            return new Response('false');
        }
    }

    /**
     * Редактирование докладчика
     *
     * @Route("/admin/speaker/{id}", name="admin-speaker-edit")
     *
     * @param integer $id
     * @param Request $request
     *
     * @return object
     */
    public function speakerEditAction($id, Request $request){
        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');

        /** @var Speaker $speaker */
        $speaker = $speakerRepository->find($id);

        /** @var Form $form */
        $form = $this->createFormBuilder($speaker)
            ->add('avatar', HiddenType::class, array('required' => false))
            ->add('avatarFile', VichFileType::class, array('label' => 'Photo', 'required' => false))
            ->add('report', TextType::class, array('label' => 'Доклад'))
            ->add('description', TextareaType::class, array('label' => 'Description'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $speaker = $form->getData();
            $speaker->setPublish(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($speaker);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );
        }

        $user = $speaker->getUser();

        return $this->render('admin/speakers/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Редактирование докладчика '.$user->getLastName().' '.$user->getFirstName(),
            'avatar' => $speaker->getAvatar(),
        ));
    }

    /**
     * Удаление докладчика
     *
     * @Route("/admin/speaker/remove/{id}", name="admin-speaker-remove")
     *
     * @param integer $id
     *
     * @return object
     */
    public function speakerRemoveAction($id){
        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');

        /** @var Speaker $speaker */
        $speaker = $speakerRepository->find($id);


        $em = $this->getDoctrine()->getManager();

        $speaker->setPublish(0);

        $em->persist($speaker);
        $em->flush();

        return $this->redirectToRoute('admin-speakers');
    }

    /**
     * Удаление организации
     *
     * @Route("/admin/table/remove/org/{id}", name="admin-table-remove-org")
     * @Route("/admin/table/remove/org/")
     *
     * @param integer|bool $id
     *
     * @return object
     */
    public function tableOrgRemoveAction($id = false){
        $result = 'OK';
        if($id){
            $em = $this->getDoctrine()->getManager();

            /** @var OrgToConfRepository $orgToConfRepository */
            $orgToConfRepository = $this->getDoctrine()->getRepository('AppBundle:OrgToConf');
            /** @var OrgToConf $orgToConf */
            $orgToConf = $orgToConfRepository->findOneBy(array('organizationId' => $id));

            $em->remove($orgToConf);
            $em->flush();

            /** @var UserRepository $userRepository */
            $userRepository = $this->getDoctrine()->getRepository('AppBundle:User');
            /** @var User $users */
            $users = $userRepository->findBy(array('organizationId' => $id));

            /** @var User $user */
            foreach ($users as $user){
                $user_id = $user->getId();
                $this->rmUser($user_id);
            }

        }
        return new Response($result);
    }

    /**
     * Удаление пользователя
     *
     * @Route("/admin/table/remove/user/{id}", name="admin-table-remove-user")
     * @Route("/admin/table/remove/user/")
     *
     * @param integer|bool $id
     *
     * @return object
     */
    public function tableUserRemoveAction($id = false){
        $result = 'OK';
        if($id) {
            $this->rmUser($id);
        }
        return new Response($result);
    }

    /**
     * @param integer|bool $id
     *
     */
    public function rmUser($id = false){
        $em = $this->getDoctrine()->getManager();

        /** @var UserToApartamentRepository $userToApartamentRepository */
        $userToApartamentRepository = $this->getDoctrine()->getRepository('AppBundle:UserToApartament');
        /** @var UserToApartament $userToApartament */
        $userToApartament = $userToApartamentRepository->findOneBy(array('userId' => $id));

        if($userToApartament) {
            $em->remove($userToApartament);
            $em->flush();
        }

        /** @var UserToConfRepository $userToConfRepository */
        $userToConfRepository = $this->getDoctrine()->getRepository('AppBundle:UserToConf');
        /** @var UserToConf $userToConf */
        $userToConf = $userToConfRepository->findOneBy(array('userId' => $id));

        if($userToConf) {
            $em->remove($userToConf);
            $em->flush();
        }
    }
    /**
     * Создание докладчика
     *
     * @Route("/admin/speakeradd", name="admin-speaker-add")
     * @param Request $request
     * @return object
     */
    public function speakerAddAction(Request $request){
//var_dump(dirname(__DIR__));
//        $id = 33;
//        /** @var SpeakerRepository $speakerRepository */
//        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
//
//        /** @var Speaker $speaker */
//        $speaker = $speakerRepository->find($id);
        $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/';
        $resize_patch = str_replace("app/../", '', $patchSave);


        $conferenceRepository = $this->getDoctrine()->getRepository('AppBundle:Conference');
        $conferences = $conferenceRepository->findBy(array(), array('year' => 'DESC'));
        $boxConferenses = array();
        foreach ($conferences as $value){
            $boxConferenses[$value->getYear()] = $value->getId();
        }
        //var_dump($boxConferenses); die();
        $good_extens = array('jpeg', 'png');
        $mimeMsg = 'Допустимые расширения файлов: '.implode(', ', $good_extens);
        $form = $this->createFormBuilder()
            ->add('avatar', HiddenType::class, array('required' => false))
            ->add('avatarFile', FileType::class, array('label' => 'Фото'))
            ->add('family', TextType::class, array('label' => 'Фамилия'))
            ->add('name', TextType::class, array('label' => 'Имя'))
            ->add('middle_name', TextType::class, array('label' => 'Отчество'))
            ->add('phone', TextType::class, array('label' => 'Телефон'))
            ->add('email', TextType::class, array('label' => 'E-mail'))
            ->add('report', TextType::class, array('label' => 'Доклад'))
            ->add('isActive', CheckboxType::class, array('label' => 'Скрыть Докладчика','required' => false ))
            ->add('conference', ChoiceType::class, array(
                'label' => 'Конференция',
                'choices'  => $boxConferenses))
            ->add('description', TextareaType::class, array('label' => 'Биография'))
            ->add('save', SubmitType::class,array('label' => 'Сохранить') )
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            echo "Вошли ебана";
            $resizeService = $this->get('resizeImages');
            $files = $form->get('avatarFile')->getData();
            $_exten = $files->getClientOriginalExtension();
            $postefixOriginal = '_original';
            $postefixSmall = '_small';
            $postefixBig = '_big';
            $uniqid = uniqid();
            $file = $files->move($patchSave,$uniqid.$postefixOriginal.'.'.$_exten);
            /* small  */
            var_dump($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->load($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->resize(400, 200);
            $resizeService->save($patchSave.$uniqid.$postefixSmall.'.'.$_exten);
            /* big */
            $resizeService->load($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->resize(800, 800);
            $resizeService->save($patchSave.$uniqid.$postefixBig.'.'.$_exten);

            //$UserRepository = $this->getDoctrine()->getRepository('AppBundle:User');
            $orgsts = $this->getDoctrine()->getRepository('AppBundle:Organization')->find(2);
            $form = $form->getData();
            $rootDir = $this->get('kernel')->getRootDir();
            $em = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setOrganization($orgsts);
            $user->setFirstName($form['name']);
            $user->setLastName($form['family']);
            $user->setMiddleName($form['middle_name']);
            $user->setUsername($form['phone']); // It's actually a phone
            $user->setEmail($form['email']);
            $user->setIsActive(1);
            $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            $user->setPassword($encoded);
            $user->setRoles(array("ROLE_USER"));
            $em->persist($user);
            $em->flush();

            $speaker = new Speaker();
            $speaker->setUser($user);
            $speaker->setAvatar($uniqid.$postefixOriginal.'.'.$_exten);
            $speaker->setAvatarSmall($uniqid.$postefixSmall.'.'.$_exten);
            $speaker->setAvatarBig($uniqid.$postefixBig.'.'.$_exten);
            $speaker->setPublish(1);
            $speaker->setConferenceId(12);
            $em->persist($speaker);
            $em->flush();


            //$speaker = $form->getData();
            return $this->redirectToRoute('admin-speakers');
        }

        /*
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $speaker = $form->getData();
            $speaker->setPublish(1);

            $em = $this->getDoctrine()->getManager();
            $em->persist($speaker);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );
        }

        $user = $speaker->getUser();
        */
        return $this->render('admin/speakers/add.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Добавление докладчика',
        ));
    }
}
