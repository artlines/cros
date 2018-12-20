<?php

namespace App\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\Speaker;
use AppBundle\Entity\SpeakerReports;
use AppBundle\Entity\Sponsor;
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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
use AppBundle\Repository\OrganizationRepository;
use ZipArchive;
class AdminMemberController extends AbstractController
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
    public function adminUsers($year = null, $signed = 'all')
    {
        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        /** @var UserToConf $users */
        $users = $this->getDoctrine()
            ->getRepository('App:UserToConf')
            ->findBy(array('conferenceId' => $conf->getId()));

        /** @var User $users */
        $users = $this->getDoctrine()
            ->getRepository('App:User')
            ->findAllByUsers($users, 0);

        $apartaments_array = array();

        $forfilter = array();

        $apartaments = $this->getDoctrine()
            ->getRepository('App:Apartament')
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
    public function saveMembers(Request $request)
    {
        $save = $request->get('save');
        $em = $this->getDoctrine()->getManager();

        if (isset($save['up'])) {
            foreach ($save['up'] as $id => $up) {
                /** @var UserToApartament $user */
                $user = $this->getDoctrine()
                    ->getRepository('App:UserToApartament')
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
                    ->getRepository('App:UserToApartament')
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
                    ->getRepository('App:UserToApartament')
                    ->findOneBy(array('userId' => $id, 'apartamentsId' => $ch['old_id']));

                if (!$user) {
                    $ru = $this->getDoctrine()
                        ->getRepository('App:User')
                        ->find($id);
                    if ($ru) {
                        $user = new UserToApartament();
                        $user->setUser($ru);
                    }
                }
                if ($user) {
                    $user->setApproved(1);

                    $apr = $this->getDoctrine()
                        ->getRepository('App:ApartamentId')
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
    public function managers(Request $request)
    {
        $year = date("Y");

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        $managers = $this->getDoctrine()
            ->getRepository('App:User')
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
    public function speakers(Request $request){
        $year = date("Y");

        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $this->getDoctrine()->getRepository('App:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(array('year' => $year));

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('App:Speaker');
        /** @var Speaker $speakers */
        $speakers = $speakerRepository->findByConf($conf->getId());

        return $this->render('admin/speakers/list.html.twig', array(

            'list' => $speakers,
        ));
    }

    /**
     * Поиск пользователя
     *
     * @Route("/admin/speaker/find", name="admin-speaker-find")
     */
    public function speakerFind(Request $request){
        $find_str = $request->get('find');
        $find_arr = explode(' ', $find_str);

        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()->getRepository('App:User');

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
    public function speakerNew(Request $request){

        $em = $this->getDoctrine()->getManager();
        $add_id = $request->get('add_id');

        $userRepository = $this->getDoctrine()->getRepository('App:User');
        $user = $userRepository->find($add_id);
        if($user) {
            $speaker = new Speaker();
            $speaker->setUser($user);
            $speaker->setPublish(1);
            $speaker->setConferenceId(12);
            $em->persist($speaker);
            $em->flush();

            return new Response($speaker->getId());
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
    public function speakerEdit($id, Request $request){
        $resizeParametr = $this->container->getParameter('speakers.resize');
        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('App:Speaker');

        /** @var Speaker $speaker */
        $speaker = $speakerRepository->find($id);
        $isActive = (boolean) $speaker->getPublish();
        $conferenceRepository = $this->getDoctrine()->getRepository('App:Conference');
        $conferences = $conferenceRepository->findBy(array(), array('year' => 'DESC'));
        $orgsts = $this->getDoctrine()->getRepository('App:Organization');
        $orgsts = $orgsts->findBy(array(), array('id' => 'ASC'));

        $em = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository('App:User')->find($speaker->getUser());





        $boxConferenses = array();
        foreach ($conferences as $value){
            $boxConferenses[$value->getYear()] = $value->getId();
        }
        $boxOrgsts = array();
        foreach ($orgsts as $org){
            $boxOrgsts[$org->getName()] = $org->getId();
        }

        $form = $this->createFormBuilder()
            ->add('avatar', HiddenType::class, array('required' => false))
            ->add('avatarFile', FileType::class, array('label' => 'Фото','required' => false))
            ->add('family', TextType::class, array('label' => 'Фамилия','data' => $user->getLastName()))
            ->add('name', TextType::class, array('label' => 'Имя','data' => $user->getFirstName()))
            ->add('middle_name', TextType::class, array('label' => 'Отчество','data' => $user->getMiddleName(),'required' => false))
            ->add('phone', TextType::class, array('label' => 'Телефон','data' => $user->getUsername()))
            ->add('email', TextType::class, array('label' => 'E-mail','data' => $user->getEmail()))
            ->add('isActive', CheckboxType::class, array('label' => 'Активный докладчик','required' => false,'data' => $isActive ))
            ->add('conference', ChoiceType::class, array(
                'label' => 'Конференция',
                'choices'  => $boxConferenses))
            ->add('organization', ChoiceType::class, array(
                'label' => 'Организация',
                'choices'  => $boxOrgsts))
            ->add('description', TextareaType::class, array('label' => 'Биография','data'=>$speaker->getDescription()))
            ->add('save', SubmitType::class,array('label' => 'Сохранить') )
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/speakers/';
            $resizeService = $this->get('resizeImages');
            $files = $form->get('avatarFile')->getData();
            if(!is_null($files)) {
                $_exten = $files->getClientOriginalExtension();
                $postefixOriginal = '_original';
                $postefixSmall = '_small';
                $postefixBig = '_big';
                $uniqid = uniqid();
                $files->move($patchSave, $uniqid . $postefixOriginal . '.' . $_exten);
                /* small  */
                $resizeService->load($patchSave . $uniqid . $postefixOriginal . '.' . $_exten);
                $resizeService->resizeSpeakers($resizeParametr['small']['width'], $resizeParametr['small']['height'],$patchSave . $uniqid . $postefixSmall . '.' . $_exten);
                /* big */
                $resizeService->load($patchSave . $uniqid . $postefixOriginal . '.' . $_exten);
                $resizeService->resizeSpeakers($resizeParametr['big']['width'], $resizeParametr['big']['height'],$patchSave . $uniqid . $postefixBig . '.' . $_exten);
            }

            $form = $form->getData();
            $orgsts = $this->getDoctrine()->getRepository('App:Organization')->find($form['organization']);
            $isActive = (int) $form['isActive'];
            $em = $this->getDoctrine()->getManager();

            $user->setOrganization($orgsts);
            $user->setFirstName($form['name']);
            $user->setLastName($form['family']);
            $user->setMiddleName($form['middle_name']);
            $form['phone'] = preg_replace('/[^0-9]/', '', $form['phone']);
            $user->setUsername($form['phone']); // It's actually a phone
            $user->setEmail($form['email']);
            $user->setIsActive($isActive);
            $em->persist($user);
            $em->flush();

            $speaker = $speakerRepository->findOneByUserId($user->getId());
            $speaker->setUser($user);
            if(!is_null($files)) {
                $speaker->setAvatar($uniqid . $postefixOriginal . '.' . $_exten);
                $speaker->setAvatarSmall($uniqid . $postefixSmall . '.' . $_exten);
                $speaker->setAvatarBig($uniqid . $postefixBig . '.' . $_exten);
            }
            $speaker->setPublish($isActive);
            $speaker->setConferenceId($form['conference']);
            $speaker->setDescription($form['description']);
            $em->persist($speaker);
            $em->flush();
            return $this->redirectToRoute('admin-speakers');
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
    public function speakerRemove($id){
        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('App:Speaker');

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
    public function tableOrgRemove($id = false){
        $result = 'OK';
        if($id){
            $em = $this->getDoctrine()->getManager();

            /** @var OrgToConfRepository $orgToConfRepository */
            $orgToConfRepository = $this->getDoctrine()->getRepository('App:OrgToConf');
            /** @var OrgToConf $orgToConf */
            $orgToConf = $orgToConfRepository->findOneBy(array('organizationId' => $id));

            $em->remove($orgToConf);
            $em->flush();

            /** @var UserRepository $userRepository */
            $userRepository = $this->getDoctrine()->getRepository('App:User');
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
    public function tableUserRemove($id = false){
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
        $userToApartamentRepository = $this->getDoctrine()->getRepository('App:UserToApartament');
        /** @var UserToApartament $userToApartament */
        $userToApartament = $userToApartamentRepository->findOneBy(array('userId' => $id));

        if($userToApartament) {
            $em->remove($userToApartament);
            $em->flush();
        }

        /** @var UserToConfRepository $userToConfRepository */
        $userToConfRepository = $this->getDoctrine()->getRepository('App:UserToConf');
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
     * @Route("/admin/speaker-add", name="admin-speaker-add")
     * @param Request $request
     * @return object
     */
    public function speakerAdd(Request $request){
        $resizeParametr = $this->container->getParameter('speakers.resize');
        $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/speakers/';
        $resize_patch = str_replace("app/../", '', $patchSave);


        $conferenceRepository = $this->getDoctrine()->getRepository('App:Conference');
        $conferences = $conferenceRepository->findBy(array(), array('year' => 'DESC'));
        $orgsts = $this->getDoctrine()->getRepository('App:Organization');
        $orgsts = $orgsts->findBy(array(), array('id' => 'ASC'));
        $boxConferenses = array();
        foreach ($conferences as $value){
            $boxConferenses[$value->getYear()] = $value->getId();
        }
        $boxOrgsts = array();
        foreach ($orgsts as $org){
            $boxOrgsts[$org->getName()] = $org->getId();
        }
        $good_extens = array('jpeg', 'png');
        $mimeMsg = 'Допустимые расширения файлов: '.implode(', ', $good_extens);
        $form = $this->createFormBuilder()
            ->add('avatar', HiddenType::class, array('required' => false))
            ->add('avatarFile', FileType::class, array('label' => 'Фото'))
            ->add('family', TextType::class, array('label' => 'Фамилия'))
            ->add('name', TextType::class, array('label' => 'Имя'))
            ->add('middle_name', TextType::class, array('label' => 'Отчество','required' => false))
            ->add('phone', TextType::class, array('label' => 'Телефон'))
            ->add('email', TextType::class, array('label' => 'E-mail'))
            ->add('isActive', CheckboxType::class, array('label' => 'Активный докладчик','required' => false,'data' => true ))
            ->add('conference', ChoiceType::class, array(
                'label' => 'Конференция',
                'choices'  => $boxConferenses))
            ->add('organization', ChoiceType::class, array(
                'label' => 'Организация',
                'choices'  => $boxOrgsts))
            ->add('description', TextareaType::class, array('label' => 'Биография'))
            ->add('save', SubmitType::class,array('label' => 'Сохранить') )
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $resizeService = $this->get('resizeImages');
            $files = $form->get('avatarFile')->getData();
            $_exten = $files->getClientOriginalExtension();
            $postefixOriginal = '_original';
            $postefixSmall = '_small';
            $postefixBig = '_big';
            $uniqid = uniqid();
            $files->move($patchSave,$uniqid.$postefixOriginal.'.'.$_exten);
            /* small  */
            $resizeService->load($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->resizeSpeakers($resizeParametr['small']['width'], $resizeParametr['small']['height'],$patchSave.$uniqid.$postefixSmall.'.'.$_exten);
            /* big */
            $resizeService->load($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->resizeSpeakers($resizeParametr['big']['width'], $resizeParametr['big']['height'],$patchSave.$uniqid.$postefixBig.'.'.$_exten);
            $form = $form->getData();
            $orgsts = $this->getDoctrine()->getRepository('App:Organization')->find($form['organization']);
            $form['phone'] = preg_replace('/[^0-9]/', '', $form['phone']);
            //$userCheck = $this->getDoctrine()->getRepository('App:User')->findOneBy(array('username' => $form['phone']));
                $isActive = (int)$form['isActive'];
                $em = $this->getDoctrine()->getManager();
                $user = new User();
                $user->setOrganization($orgsts);
                $user->setFirstName($form['name']);
                $user->setLastName($form['family']);
                $user->setMiddleName($form['middle_name']);
                $user->setUsername($form['phone']); // It's actually a phone
                $user->setEmail($form['email']);
                $user->setIsActive($isActive);
                $password = substr(md5($user->getLastName() . $user->getFirstName()), 0, 6);
                $encoder = $this->container->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $password);
                $user->setPassword($encoded);
                $user->setRoles(array("ROLE_USER"));
                $em->persist($user);
                $em->flush();

                $speaker = new Speaker();
                $speaker->setUser($user);
                $speaker->setAvatar($uniqid . $postefixOriginal . '.' . $_exten);
                $speaker->setAvatarSmall($uniqid . $postefixSmall . '.' . $_exten);
                $speaker->setAvatarBig($uniqid . $postefixBig . '.' . $_exten);
                $speaker->setPublish($isActive);
                $speaker->setConferenceId($form['conference']);
                $speaker->setDescription($form['description']);
                $em->persist($speaker);
                $em->flush();

            //$speaker = $form->getData();
            return $this->redirectToRoute('admin-speakers');
        }
        return $this->render('admin/speakers/add.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Добавление докладчика',
        ));
    }
    /**
     * Список докладов
     *
     * @Route("/admin/speakers/list-report/{id}", name="speakers-list-report")
     *
     * @param Request $request
     * @return object
     */
    public function ListReport($id){
        $year = date("Y");

        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $this->getDoctrine()->getRepository('App:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(array('year' => $year));

        $speakerReportsRepository = $this->getDoctrine()->getRepository('App:SpeakerReports');
        $report = $speakerReportsRepository->findBy(array('speaker_id' => $id));
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('label' => 'Название','required' => true))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();

        return $this->render('admin/speakers/list_reports.html.twig', array(

            'list' => $report,
            'form' => $form->createView(),
            'id' => $id
        ));
    }
    /**
     * Добавление доклада
     *
     * @Route("/admin/speakers/add-report/{id}", name="speakers-add-report")
     *
     * @param integer $id
     * @param Request $request
     * @return object
     */
    public function addReport($id,Request $request){
        $speakerReportsRepository = $this->getDoctrine()->getRepository('App:SpeakerReports');
        $speakerRepository = $this->getDoctrine()->getRepository('App:Speaker');
        $speaker = $speakerRepository->find($id);
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, array('label' => 'Название','required' => true))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $form = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $speakerReports = new SpeakerReports();
            $speakerReports->setReport($form['name']);
            $speakerReports->setSpeaker($id);
            $em->persist($speakerReports);
            $em->flush();
        }

        $urlParameters[ "id" ] = $id ;
        return $this->redirectToRoute('speakers-list-report',$urlParameters);

    }
    /**
     * Удаление доклада
     *
     * @Route("/admin/speakers/delete-report/{id}/{user_id}", name="speakers-delete-report")
     *
     * @param integer $id
     * @param integer $user_id
     * @return object
     */
    public function deleteReport($id,$user_id){
        $speakerReportsRepository = $this->getDoctrine()->getRepository('App:SpeakerReports');
        $report = $speakerReportsRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($report);
        $em->flush();
        $urlParameters[ "id" ] = $user_id ;
        return $this->redirectToRoute('speakers-list-report',$urlParameters);

    }
    /**
     * Спонсоры
     *
     * @Route("/admin/sponsor", name="admin-sponsor-list")
     *
     * @param Request $request
     * @return object
     */
    public function sponsorList(Request $request){
      
        $RepositorySponsor = $this->getDoctrine()->getRepository('App:Sponsor');
        /** @var Sponsor $sponsor */
        $sponsor = $RepositorySponsor->findAll();
        $formImport = $this->createFormBuilder()
            ->add('csv', FileType::class, array('label' => 'csv','required' => true))
            ->add('zip', FileType::class, array('label' => 'архив с логотипами','required' => true))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();

        return $this->render('admin/sponsor/list.html.twig', array(

            'form' => $formImport->createView(),
            'list' => $sponsor,
        ));
    }
    /**
     * Добавление спонсора
     *
     * @Route("/admin/sponsor/add", name="sponsor-add")
     * @param Request $request
     * @return object
     */
    public function addSponsor(Request $request){
        $resizeParametr = $this->container->getParameter('sponsor.resize');
        $RepositoryTypeSponsor = $this->getDoctrine()->getRepository('App:TypeSponsor');
        $typeSponsor = $RepositoryTypeSponsor->findAll();
        $typeSponsorList = null;
        foreach ($typeSponsor as $value){ // This is necessary for select, and then he does not know how else
            $typeSponsorList[$value->getNameType()] = $value->getId();
        }

        $form = $this->createFormBuilder()
            ->add('avatarFile', FileType::class, array('label' => 'Фото','required' => true))
            ->add('name', TextType::class, array('label' => 'Наименование','required' => true))
            ->add('url', TextType::class, array('label' => 'Сайт','required' => true))
            ->add('phone', TextType::class, array('label' => 'Телефон','required' => true))
            ->add('type', ChoiceType::class, array(
                'label' => 'Тип спонсора',
                'choices'  => $typeSponsorList))
            ->add('isActive', CheckboxType::class, array('label' => 'Показывать спонсора','required' => false,'data' => true ))
            ->add('description', TextareaType::class, array('label' => 'Описание'))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();


        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/sponsor/'; // replase to config for prod!!!
            $resizeService = $this->get('resizeImages');
            $files = $form->get('avatarFile')->getData();
            $_exten = $files->getClientOriginalExtension();
            $postefixOriginal = '_original';
            $postefixResize = '_resize';
            $uniqid = uniqid();
            $files->move($patchSave,$uniqid.$postefixOriginal.'.'.$_exten);
            /* resize  */
            $resizeService->load($patchSave.$uniqid.$postefixOriginal.'.'.$_exten);
            $resizeService->resizeSponsor($resizeParametr['width'], $resizeParametr['height'],$patchSave.$uniqid.$postefixResize.'.'.$_exten);
            $form = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $sponsor = new Sponsor();
            $sponsor->setActive($form['isActive']);
            $sponsor->setLogo($uniqid.$postefixOriginal.'.'.$_exten);
            $sponsor->setLogoResize($uniqid.$postefixResize.'.'.$_exten);
            $sponsor->setName($form['name']);
            $form['phone'] = preg_replace('/[^0-9]/', '', $form['phone']);
            $sponsor->setPhone($form['phone']);
            $sponsor->setUrl($form['url']);
            $sponsor->setDescription($form['description']);
            $sponsor->setTypeSponsor($RepositoryTypeSponsor->find($form['type']));
            $em->persist($sponsor);
            $em->flush();
            return $this->redirectToRoute('admin-sponsor-list');

        }

        return $this->render('admin/sponsor/add.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Добавление спонсора',
        ));

    }
    /**
     * Редактирование спонсора
     *
     * @Route("/admin/sponsor-edit/{id}", name="admin-sponsor-edit")
     *
     * @param integer $id
     * @param Request $request
     *
     * @return object
     */
    public function sponsorEdit($id, Request $request){
        $resizeParametr = $this->container->getParameter('sponsor.resize');
        $RepositoryTypeSponsor = $this->getDoctrine()->getRepository('App:TypeSponsor');
        $RepositorySponsor = $this->getDoctrine()->getRepository('App:Sponsor');
        $typeSponsor = $RepositoryTypeSponsor->findAll();
        $typeSponsorList = null;
        foreach ($typeSponsor as $value){ // This is necessary for select, and then he does not know how else
            $typeSponsorList[$value->getNameType()] = $value->getId();
        }
        $sponsor = $RepositorySponsor->find($id);

        $form = $this->createFormBuilder()
            ->add('avatarFile', FileType::class, array('label' => 'Фото','required' => false))
            ->add('name', TextType::class, array('label' => 'Наименование','required' => true,'data'=>$sponsor->getName()))
            ->add('url', TextType::class, array('label' => 'Сайт','required' => true,'data' => $sponsor->getUrl()))
            ->add('phone', TextType::class, array('label' => 'Телефон','required' => true, 'data' => $sponsor->getPhone()))
            ->add('type', ChoiceType::class, array(
                'label' => 'Тип спонсора',
                'choices'  => $typeSponsorList))
            ->add('isActive', CheckboxType::class, array('label' => 'Показывать спонсора','required' => false,'data' => $sponsor->getIsActive() ))
            ->add('description', TextareaType::class, array('label' => 'Описание','data'=>$sponsor->getDescription()))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/sponsor/'; // replase to config for prod!!!
            $resizeService = $this->get('resizeImages');
            $files = $form->get('avatarFile')->getData();
            if(!is_null($files)) {
                $_exten = $files->getClientOriginalExtension();
                $postefixOriginal = '_original';
                $postefixResize = '_resize';
                $uniqid = uniqid();
                $files->move($patchSave, $uniqid . $postefixOriginal . '.' . $_exten);
                /* resize  */
                $resizeService->load($patchSave . $uniqid . $postefixOriginal . '.' . $_exten);
                $resizeService->resizeSponsor($resizeParametr['width'],$resizeParametr['height'],$patchSave . $uniqid . $postefixResize . '.' . $_exten);
               // $resizeService->save($patchSave . $uniqid . $postefixResize . '.' . $_exten);
            }
            $form = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $sponsor->setActive($form['isActive']);
            if(!is_null($files)) {
                $sponsor->setLogo($uniqid . $postefixOriginal . '.' . $_exten);
                $sponsor->setLogoResize($uniqid . $postefixResize . '.' . $_exten);
            }
            $sponsor->setName($form['name']);
            $form['phone'] = preg_replace('/[^0-9]/', '', $form['phone']);
            $sponsor->setPhone($form['phone']);
            $sponsor->setUrl($form['url']);
            $sponsor->setDescription($form['description']);
            $sponsor->setTypeSponsor($RepositoryTypeSponsor->find($form['type']));
            $em->persist($sponsor);
            $em->flush();
            return $this->redirectToRoute('admin-sponsor-list');

        }



        return $this->render('admin/sponsor/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Редактирование спонсора ',
            'avatar' => $sponsor->getLogo(),
        ));
    }
    /**
     * Удаление спонсора
     *
     * @Route("/admin/sponsor/delete/{id}", name="admin-sponsor-delete")
     *
     * @param integer $id
     * @return object
     */
    public function deleteSponsor($id){
        $RepositorySponsor = $this->getDoctrine()->getRepository('App:Sponsor');
        $sponsor = $RepositorySponsor->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($sponsor);
        $em->flush();

        return $this->redirectToRoute('admin-sponsor-list');

    }
    /**
     * Импорт спонсоров из csv
     *
     * @Route("/admin/sponsor/csv-import", name="admin-sponsor-import-scv")
     *
     * @param Request $request
     * @return object
     */
    public function importCsvSponsor(Request $request){
        $form = $this->createFormBuilder()
            ->add('csv', FileType::class, array('label' => 'csv','required' => true))
            ->add('zip', FileType::class, array('label' => 'архив с логотипами','required' => true))
            ->add('save', SubmitType::class,array('label' => 'Сохранить'))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $RepositoryTypeSponsor = $this->getDoctrine()->getRepository('App:TypeSponsor');
            $resizeService = $this->get('resizeImages');
            $patchSave = $this->get('kernel')->getRootDir().'/../web/uploads/sponsor/';
            $postefixOriginal = '_original';
            $postefixResize = '_resize';
            $csv = $form->get('csv')->getData();
            $zip = $form->get('zip')->getData();
            $_extenZip = $zip->getClientOriginalExtension();
            $_extenCsv = $csv->getClientOriginalExtension();
            $uniqid = uniqid();
            $zip->move($patchSave, $uniqid.'.'.$_extenZip);
            $csv->move($patchSave, $uniqid.'.'.$_extenCsv);
            $zip = new ZipArchive();
            $zip->open($patchSave.$uniqid.'.'.$_extenZip);
            $numFiles = $zip->numFiles;
            $listZipFiles = NULL;
            for ($i=0; $i<$numFiles; $i++) {
                $zipIndex = $zip->statIndex($i)['name'];
                $filename = explode(".", $zipIndex);
                $listZipFiles[$zipIndex]['name'] = uniqid();
                $listZipFiles[$zipIndex]['ex'] = '.'.end($filename);
            }
            $zip->extractTo($patchSave);
            $zip->close();
            unlink($patchSave.$uniqid.'.'.$_extenZip);
            foreach ($listZipFiles as $key => $value){
                rename($patchSave.$key,$patchSave.$value['name'].$postefixOriginal.$value['ex']);
            }
            foreach ($listZipFiles as $value){
                $resizeService->load($patchSave.$value['name'].$postefixOriginal.$value['ex']);
                $resizeService->resizeToWidth(200);
                $resizeService->save($patchSave .$value['name'].$postefixResize.$value['ex']);
            }
            $csvimport=NULL;
            if (($handle = fopen($patchSave.$uniqid.'.'.$_extenCsv, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csvimport[] = $data;
                }
                fclose($handle);
            }
            unset($csvimport[0]);
            unlink($patchSave.$uniqid.'.'.$_extenCsv);
            $em = $this->getDoctrine()->getManager();
            foreach ($csvimport as $key => $value){
                $fileLoad = $listZipFiles[$value[2]];
                $sponsor = new Sponsor();
                $sponsor->setActive($value[3]);
                $sponsor->setLogo($fileLoad['name'].$postefixOriginal.$fileLoad['ex']);
                $sponsor->setLogoResize($fileLoad['name'].$postefixResize.$fileLoad['ex']);
                $sponsor->setName($value[0]);
                $sponsor->setPhone(str_replace("+", '', $value[1]));
                $sponsor->setDescription($value[5]);
                $sponsor->setUrl($value[6]);
                $typeSponsor = $RepositoryTypeSponsor->findOneBy(array('name_type' => $value[4]));
                $sponsor->setTypeSponsor($typeSponsor);
                $em->persist($sponsor);
                $em->flush();

            }
        }
        return $this->redirectToRoute('admin-sponsor-list');
    }
}
