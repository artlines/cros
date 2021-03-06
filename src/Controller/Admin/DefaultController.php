<?php

namespace App\Controller\Admin;

use App\Old\Entity\Conference;
use App\Old\Entity\Organization;
use App\Old\Entity\OrganizationStatus;
use App\Repository\ConferenceRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @Route("/admin/download/csv-hotel-2018", name="download-hotel-csv-2018")
     */
    public function getCsvHotel()
    {
        /** @var FlatRepository $flatRepo */
        $flatRepo = $this->getDoctrine()->getRepository('App:Flat');
        $data = $flatRepo->findAllToHotel();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=hotel_2018.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: text/html; charset=windows-1251');

        $Response = $this->render('admin/download/csv_hotel_2018.html.twig', array(
            'data' => $data
        ));

        $Response->setCharset("WINDOWS-1251");
        $Response->setContent( mb_convert_encoding( $Response->getContent() , "WINDOWS-1251",  "UTF-8" ));

        return  $Response;
    }

    /**
     * История
     *
     * @Route("/admin/history", name="admin-history")
     * @Route("/admin")
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function index(EntityManagerInterface $em)
    {
        $history = $em->getRepository('App:Conference')->findBy([], ['year' => 'DESC']);

        return $this->render('admin/history.html.twig', [
            'history' => $history,
        ]);
    }

    /**
     * Редактирование конференции
     *
     * @Route("/admin/date/{id}", name="admin-date-edit")
     *
     * @param integer|string $id
     * @param Request $request
     *
     * @return object
     */
    public function editDate($id, Request $request)
    {
        $result = false;

        if ($id == 'new') {
            $conf = new Conference();
            $new = true;
        } else {
            /** @var ConferenceRepository $ConferenceRepository */
            $ConferenceRepository = $this->getDoctrine()
                ->getRepository('App:Conference');
            /** @var Conference $conf */
            $conf = $ConferenceRepository->find($id);
            if (!$conf) {
                return $this->redirectToRoute('admin-date-edit', array('id' => 'new'));
            }
            $new = false;
        }

        /** @var Form $form */
        $form = $this->createFormBuilder($conf)
            ->add('registration_start', DateTimeType::class, array('label' => 'Start registration'))
            ->add('registration_finish', DateTimeType::class, array('label' => 'Finish registration'))
            ->add('start', DateTimeType::class, array('label' => 'Start event'))
            ->add('finish', DateTimeType::class, array('label' => 'Finish event'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conf = $form->getData();
            /** @var \DateTime $conf_start */
            $conf_start = $conf->getStart();
            $year = $conf_start->format("Y");
            $conf->setYear($year);

            $em = $this->getDoctrine()->getManager();
            $em->persist($conf);
            $em->flush();

            if ($id == 'new') {
                $aliases = array(
                    'targets' => 'Цели и задачи',
                    'place' => 'Место проведения',
                    'transfer' => 'Доставка участников',
                    'terms' => 'Докладчикам и партнерам',
                    'result' => 'Итоги',
                );

                foreach ($aliases as $alias => $title) {
                    $info = new Info();
                    $info->setAlias($alias);
                    $info->setTitle($title);
                    $info->setContent($year);

                    $em->persist($info);
                    $em->flush();

                    $info_to_conf = new InfoToConf();
                    $info_to_conf->setInfo($info);
                    $info_to_conf->setConference($conf);

                    $em->persist($info_to_conf);
                    $em->flush();
                }
            }

            return $this->redirectToRoute('admin-history');
        }

        return $this->render('admin/conference/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Даты',
            'new' => $new,
            'result' => $result,
        ));
    }

    /**
     * Сводная таблица
     *
     * @Route("/admin/table/{year}", name="admin-table")
     * @Route("/admin/table")
     *
     * @param integer|null $year
     * @param Request $request
     * @return object
     */
    public function table($year = null, Request $request)
    {
        $group_sort = $request->get('group');
        $show_empty = (bool) $request->get('show_empty');

        if ($year == null) {
            $year = date("Y");
        }
        $full_table = array(1);

        $members_count = 0;

        /** @var ConferenceRepository $ConferenceRepository */
        $ConferenceRepository = $this->getDoctrine()
            ->getRepository('App:Conference');

        /** @var Conference $conf */
        $conf = $ConferenceRepository->findOneBy(array('year' => $year));

        /** @var OrgToConfRepository $OrgToConfRepository */
        $OrgToConfRepository = $this->getDoctrine()
            ->getRepository('App:OrgToConf');

        /** @var OrgToConf $org_ids */
        $org_ids = $OrgToConfRepository->findBy(array('conferenceId' => $conf->getId()));

        $org_ids_array = array();

        /** @var OrgToConf $org_id */
        foreach ($org_ids as $org_id) {
            $org_ids_array[] = $org_id->getOrganizationId();
        }

        unset($org_ids);

        $man_gr_id = false;
        $approved = false;
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            /** @var User $man */
            $man = $this->getUser();
            $man_gr_id = $man->getManagerGroupId();
        }
        elseif ($group_sort != null && $group_sort != 0){
            $man_gr_id = $group_sort;
            $approved = true;
        }

        /** @var OrganizationRepository $OrganizationRepository */
        $OrganizationRepository = $this->getDoctrine()
            ->getRepository('App:Organization');

        /** @var Organization $organizations */
        $organizations = $OrganizationRepository->findByIdsWithConfUser($org_ids_array, $conf->getId(), $man_gr_id, $approved, $show_empty);

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $users = $organization->getUsers();
            $members_count += count($users);
        }

        /** @var ApartamentIdRepository $ApartamentIdRepository */
        $ApartamentIdRepository = $this->getDoctrine()
            ->getRepository('App:ApartamentId');

        /** @var ManagerGroupRepository $ManagerGroupRepository */
        $ManagerGroupRepository = $this->getDoctrine()
            ->getRepository('App:ManagerGroup');

        /** @var $manager_groups $manager_groups */
        $manager_groups = $ManagerGroupRepository->findAll();

        /** @var ApartamentId $numbers_full */
        $numbers_full = $ApartamentIdRepository->findAllWithUser($conf->getId());

        return $this->render('admin/table/show.html.twig', array(
            'fulltable' => $full_table,
            'conf' => $conf,
            'organizations' => $organizations,
            'numbersfull' => $numbers_full,
            'memberscount' => $members_count,
            'managergroups' => $manager_groups,
            'groupsort' => $group_sort,
        ));
    }

    /**
     * Сводная таблица
     *
     * @Route("/admin/table2/{year}", name="admin-table2")
     * @Route("/admin/table2")
     *
     * @param integer|null $year
     * @param Request $request
     * @return object
     */
    public function table2($year = null, Request $request)
    {
        $group_sort = $request->get('group');

        if ($year == null) {
            $year = date("Y");
        }
        $full_table = array(1);

        $members_count = 0;

        /** @var ConferenceRepository $ConferenceRepository */
        $ConferenceRepository = $this->getDoctrine()
            ->getRepository('App:Conference');

        /** @var Conference $conf */
        $conf = $ConferenceRepository->findOneBy(array('year' => $year));

        /** @var OrgToConfRepository $OrgToConfRepository */
        $OrgToConfRepository = $this->getDoctrine()
            ->getRepository('App:OrgToConf');

        /** @var OrgToConf $org_ids */
        $org_ids = $OrgToConfRepository->findBy(array('conferenceId' => $conf->getId()));

        $org_ids_array = array();

        /** @var OrgToConf $org_id */
        foreach ($org_ids as $org_id) {
            $org_ids_array[] = $org_id->getOrganizationId();
        }

        unset($org_ids);

        $man_gr_id = false;
        $approved = false;
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            /** @var User $man */
            $man = $this->getUser();
            $man_gr_id = $man->getManagerGroupId();
        }
        elseif ($group_sort != null && $group_sort != 0){
            $man_gr_id = $group_sort;
            $approved = true;
        }

        /** @var OrganizationRepository $OrganizationRepository */
        $OrganizationRepository = $this->getDoctrine()
            ->getRepository('App:Organization');

        /** @var Organization $organizations */
        $organizations = $OrganizationRepository->findByIdsWithConfUser($org_ids_array, $conf->getId(), $man_gr_id, $approved);

        /** @var Organization $organization */
        foreach ($organizations as $organization) {
            $users = $organization->getUsers();
            $members_count += count($users);
        }

        /** @var ApartamentIdRepository $ApartamentIdRepository */
        $ApartamentIdRepository = $this->getDoctrine()
            ->getRepository('App:ApartamentId');

        /** @var ManagerGroupRepository $ManagerGroupRepository */
        $ManagerGroupRepository = $this->getDoctrine()
            ->getRepository('App:ManagerGroup');

        /** @var $manager_groups $manager_groups */
        $manager_groups = $ManagerGroupRepository->findAll();

        /** @var ApartamentId $numbers_full */
        $numbers_full = $ApartamentIdRepository->findAllWithUser($conf->getId());

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=2017.csv');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Type: text/html; charset=windows-1251');

        $Response = $this->render('admin/table/print.html.twig', array(
            'fulltable' => $full_table,
            'conf' => $conf,
            'organizations' => $organizations,
            'numbersfull' => $numbers_full,
            'memberscount' => $members_count,
            'managergroups' => $manager_groups,
            'groupsort' => $group_sort,
        ));
//        var_dump( $Response->Content );
        $Response->setCharset("WINDOWS-1251");
        $Response->setContent( mb_convert_encoding( $Response->getContent() , "WINDOWS-1251",  "UTF-8" ));

        return  $Response;

        //exit();
    }


    /**
     * Сохранение комментария
     *
     * @Route("/admin/save_comment", name="admin-save-comment")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveComment(Request $request)
    {
        $data = $request->get('data');
        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();
        /** @var Organization $org */
        $org = $this->getDoctrine()
            ->getRepository('App:Organization')
            ->find($id);

        $org->setOurComment($data);

        $em->persist($org);
        $em->flush();

        $response = new JsonResponse('ok');
        return $response;
    }

    /**
     * Добавление пользователя
     *
     * @Route("/admin/member/add/{orgid}", name="admin-member-add")
     *
     * @param int $orgid
     * @param Request $request
     * @return object
     */
    public function addMember($orgid, Request $request){

        $year = date('Y');
        $em = $this->getDoctrine()->getManager();

        $conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        $org = $this->getDoctrine()->getRepository('App:Organization')
            ->find($orgid);

        $user = new User();
        $user->setOrganization($org);
        $user->setFirstName('Участник');
        $user->setLastName('Новый');
        $user->setUsername('70000'.date('YmHis'));
        $user->setEmail('needsetemail@gmail.com');
        $user->setIsActive(1);
        $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);

        $user->setPassword($encoded);
        $user->setRoles(array("ROLE_USER"));
        $em->persist($user);
        $em->flush();

        $usertoconf = new UserToConf();
        $usertoconf->setUser($user);
        $usertoconf->setConferenceId($conf->getId());
        $em->persist($usertoconf);
        $em->flush();


        return $this->redirectToRoute('admin-member-edit', array('id' => $user->getId()));
    }

    /**
     * Редактирование пользователя
     *
     * @Route("/admin/member/{id}", name="admin-member-edit")
     *
     * @param int $id
     * @param Request $request
     * @return object
     */
    public function editMember($id, Request $request){
        $year = date('Y');
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository('App:User')
            ->find($id);

        $em = $this->getDoctrine()->getManager();

        /** @var Form $form */
        $form = $this->createFormBuilder($user)
            ->add('last_name', TextType::class, array('label' => 'Last name'))
            ->add('first_name', TextType::class, array('label' => 'First name'))
            ->add('middle_name', TextType::class, array('label' => 'Middle name', 'required' => false))
            ->add('post', TextType::class, array('label' => 'Post', 'required' => true))
            ->add('email', EmailType::class, array('label' => 'E-mail'))
            ->add('username', TextType::class, array('label' => 'Mobile phone', 'attr' => array('data-helper' => 'Телефон для связи', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
            ->add('nickname', TextType::class, array('label' => 'Nickname', 'required' => false))
            ->add('car_number', TextType::class, array('label' => 'Если Вы приедете на личном транспорте, укажите его государственный номер', 'required' => false, 'attr' => array('placeholder' => 'А001АА 00', 'title' => 'А001АА 00')))
            ->add('arrival', DateType::class, array('label' => 'Дата заезда', 'required' => false, 'years' => array(date('Y')), 'placeholder' => array('year' => 'Год', 'month' => 'Месяц', 'day' => 'День'), 'attr' => array()))
            ->add('female', CheckboxType::class, array('label' => 'Женщина', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            $em->persist($user);
            $em->flush();


            return $this->redirectToRoute('admin-member-edit', array('id' => $user->getId()));
        }

        return $this->render('admin/table/edit_member.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));

    }

    /**
     * Добавление организации
     *
     * @Route("/admin/org/new", name="admin-org-add")
     *
     * @param Request $request
     * @return object
     */
    public function addOrg(Request $request){
        $year = date('Y');

        $org = new Organization();

        $em = $this->getDoctrine()->getManager();

        $orgsts = $this->getDoctrine()
            ->getRepository('App:OrganizationStatus')
            ->findAll();

        $choices = array();
        /** @var OrganizationStatus $orgst */
        foreach ($orgsts as $orgst) {
            $choices[$orgst->getTitle()] = $orgst->getId();
        }

        $managers = array('Без менеджера' => null);
        $managers_query = $this->getDoctrine()
            ->getRepository('App:ManagerGroup')
            ->findAll();

        /** @var ManagerGroup $manager_query */
        foreach ($managers_query as $manager_query) {
            $managers[$manager_query->getTitle()] = $manager_query->getId();
        }

        /** @var Form $form */
        $form = $this->createFormBuilder($org)
            ->add('status', ChoiceType::class, array('label' => 'Статус', 'choices' => $choices))
            ->add('name', TextType::class, array('label' => 'Название организации', 'required' => true, 'attr' => array('placeholder' => 'Ёлки-телеком')))
            ->add('city', TextType::class, array('label' => 'City', 'required' => true, 'attr' => array('placeholder' => 'Екатеринбург')))
            ->add('email', EmailType::class, array('label' => 'E-mail', 'required' => true))
            ->add('username', TextType::class, array('label' => 'Телефон', 'required' => true, 'attr' => array('pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
            ->add('inn', TextType::class, array('label' => 'ИНН', 'required' => true))
            ->add('kpp', TextType::class, array('label' => 'КПП', 'required' => false))
            ->add('requisites', TextareaType::class, array('label' => 'Реквизиты', 'required' => false))
            ->add('address', TextareaType::class, array('label' => 'Address', 'required' => false))
            ->add('manager', ChoiceType::class, array('label' => 'Менеджер', 'choices' => $managers))
            ->add('sponsor', CheckboxType::class, array('label' => 'Спонсор', 'required' => false))
            ->add('hidden', CheckboxType::class, array('label' => 'Скрыть из списка участников', 'required' => false))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Organization $org */
            $org = $form->getData();

            $password = substr(md5($org->getName()), 0, 6);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($org, $password);

            $org->setPassword($encoded);

            $org->setIsActive(1);

            $status = $this->getDoctrine()->getRepository('App:OrganizationStatus')
                ->find($org->getStatus());

            $org->setTxtstatus($status);

            $em->persist($org);
            $em->flush();

            $conf = $this->getDoctrine()
                ->getRepository('App:Conference')
                ->findOneBy(array('year' => $year));

            $otc = new OrgToConf();
            $otc->setConference($conf);
            $otc->setOrganization($org);

            $em->persist($otc);
            $em->flush();

            $user = new User();
            $user->setOrganization($org);
            $user->setFirstName('Участник');
            $user->setLastName('Новый');
            $user->setUsername('70000'.date('YmHis'));
            $user->setEmail('needsetemail@gmail.com');
            $user->setIsActive(1);
            $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);

            $user->setPassword($encoded);
            $user->setRoles(array("ROLE_USER"));
            $em->persist($user);
            $em->flush();

            $usertoconf = new UserToConf();
            $usertoconf->setUser($user);
            $usertoconf->setConferenceId($conf->getId());
            $em->persist($usertoconf);
            $em->flush();


            return $this->redirectToRoute('admin-org-manage', array('id' => $org->getId()));
        }

        return $this->render('admin/table/edit_org.html.twig', array(
            'form' => $form->createView(),
            'org' => $org->getName(),
        ));
    }

    /**
     * Управление счетами
     *
     * @Route("/admin/org/{id}", name="admin-org-manage")
     *
     * @param int $id
     * @param Request $request
     * @return object
     */
    public function editOrg($id = null, Request $request, EntityManagerInterface $em)
    {
        $year = date('Y');
        $pay_summ = $pay_date = $pay_invoice = $paid_summ = 0;

        /** @var Conference $conf */
        $conf = $em->getRepository('App:Conference')->findOneBy(['year' => $year]);

        /** @var Organization $org */
        $org = $em->getRepository('App:Organization')->find($id);

        /** @var OrganizationStatus[] $orgsts */
        $orgsts = $em->getRepository('App:OrganizationStatus')->findAll();

        $choices = array();
        foreach ($orgsts as $orgst) {
            $choices[$orgst->getTitle()] = $orgst->getId();
        }

        $paids = $org->getOtc();

        $pay_st = null;
        /** @var OrgToConf $paid */
        foreach ($paids as $paid) {
            if ($paid->getConferenceId() == $conf->getId()) {
                $pay_st = $paid->getPaid();
                $pay_summ = $paid->getSumm();
                $paid_summ = $paid->getPaidSum();
                $pay_invoice = $paid->getInvoice();
                $pay_date = $paid->getPaymentDate();
            }
        }
        if ($pay_summ == 0) {
            $usrs = $org->getUsers();
            /** @var User $usr */
            foreach ($usrs as $usr) {
                $utas = $usr->getUtoas();
                /** @var UserToApartament $uta */
                foreach ($utas as $uta) {
                    $apids = $uta->getApartament();
                    /** @var ApartamentId $apids */
                    foreach ($apids as $apid) {
                        // var_dump($apid->getApartamentId()); die();
                        //    $ap = $apid->getApartament();
                        //    $pay_summ += $ap->getPrice();
                    }
                }
            }
        }
        $p_st = array(
            'Не оплачено' => 0,
            'Оплачено' => 1,
            'Частично оплачено' => 2
        );
        if ($pay_summ == null) {
            $pay_summ = 0;
        }

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            /** @var Form $form */
            $form = $this->createFormBuilder($org)
                ->add('summ', TextType::class, array('label' => 'Сумма счета', 'mapped' => false, 'data' => $pay_summ))
                ->add('invoice', TextType::class, array('label' => 'Номер счета', 'mapped' => false, 'data' => $pay_invoice))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();
        } else {
            $managers = array('Без менеджера' => null);
            $managers_query = $this->getDoctrine()
                ->getRepository('App:ManagerGroup')
                ->findAll();

            /** @var ManagerGroup $manager_query */
            foreach ($managers_query as $manager_query) {
                $managers[$manager_query->getTitle()] = $manager_query->getId();
            }
            /** @var Form $form */
            $form = $this->createFormBuilder($org)
                ->add('status', ChoiceType::class, array('label' => 'Статус', 'choices' => $choices))
                ->add('paid', ChoiceType::class, array('label' => 'Оплата', 'choices' => $p_st, 'data' => $pay_st, 'mapped' => false))
                ->add('paid_summ', TextType::class, array('label' => 'Сумма оплаты', 'mapped' => false, 'data' => $paid_summ, 'required' => false))
                ->add('summ', TextType::class, array('label' => 'Сумма счета', 'mapped' => false, 'data' => $pay_summ, 'required' => false))
                ->add('invoice', TextType::class, array('label' => 'Номер счета', 'mapped' => false, 'data' => $pay_invoice, 'required' => false))
                ->add('payment_date', DateTimeType::class, array('label' => 'Дата оплаты', 'mapped' => false, 'data' => $pay_date))
                ->add('name', TextType::class, array('label' => 'Название организации', 'required' => true, 'attr' => array('placeholder' => 'Ёлки-телеком', 'data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('city', TextType::class, array('label' => 'City', 'required' => false, 'attr' => array('placeholder' => 'Екатеринбург', 'data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('email', EmailType::class, array('label' => 'E-mail', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('username', TextType::class, array('label' => 'Телефон', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
                ->add('inn', TextType::class, array('label' => 'ИНН', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('kpp', TextType::class, array('label' => 'КПП', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('requisites', TextareaType::class, array('label' => 'Реквизиты', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('address', TextareaType::class, array('label' => 'Address', 'required' => false, 'attr' => array('data-helper' => 'Не редактируйте данное поле без необходимости')))
                ->add('manager', ChoiceType::class, array('label' => 'Менеджер', 'choices' => $managers))
                ->add('sponsor', CheckboxType::class, array('label' => 'Спонсор', 'required' => false))
                ->add('hidden', CheckboxType::class, array('label' => 'Скрыть из списка участников', 'required' => false))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $org = $form->getData();

            if ($form->has('paid')) {
                $paid = $form->get('paid')->getData();
            }
            $summ = $form->get('summ')->getData();
            $invoice = $form->get('invoice')->getData();

            if ($form->has('payment_date')) {
                $payment_date = $form->get('payment_date')->getData();
            }
            if ($form->has('paid_summ')) {
                $paid_summ = $form->get('paid_summ')->getData();
            }

            /** @var OrgToConf $otc */
            $otc = $this->getDoctrine()
                ->getRepository('App:OrgToConf')
                ->findOneBy(array('organizationId' => $org->getId(), 'conferenceId' => $conf->getId()));

            if ($form->has('paid')) {
                $otc->setPaid($paid);
            }
            $otc->setSumm($summ);
            $otc->setInvoice($invoice);
            if ($form->has('paid_summ')) {
                $otc->setPaidSum($paid_summ);
            }
            if ($form->has('payment_date')) {
                $otc->setPaymentDate($payment_date);
            }

            $em->persist($org);

            $em->persist($otc);
            $em->flush();
        }

        return $this->render('admin/table/edit_org.html.twig', array(
            'form' => $form->createView(),
            'org' => $org->getName(),
        ));
    }

    /**
     * @Route("/admin/download", name="downloads")
     * @return object
     */
    public function downloads()
    {
        return $this->render('admin/download/list.html.twig', array());
    }


    /**
     * Генератор CSV
     *
     * @Route("/admin/download/{format}/{filename}", name="download")
     *
     * @param string $format
     * @param string $filename
     * @param Request $request
     *
     * @return object
     */
    public function download($format, $filename, Request $request)
    {
        $print = $request->query->has('print');
        $woinvoice = $request->query->has('woinvoice');
        $wopaid = $request->query->has('wopaid');
        $gupandorkk_check = $request->query->has('gupandorkk');

        if($gupandorkk_check){
            $gupandorkk = array(4, 5);
        }
        else{
            $gupandorkk = false;
        }

        //require_once '';

        $year = date('Y');

        /** @var ConferenceRepository $ConferenceRepository */
        $ConferenceRepository = $this->getDoctrine()
            ->getRepository('App:Conference');

        /** @var Conference $conf */
        $conf = $ConferenceRepository->findOneBy(array('year' => $year));

        if ($filename == 'general') {

            /** @var OrganizationRepository $OrganizationRepository */
            $OrganizationRepository = $this->getDoctrine()
                ->getRepository('App:Organization');

            /** @var Organization $orgs */
            $orgs = $OrganizationRepository
                ->findAllByConference($conf->getId(), false, $gupandorkk);

            switch ($filename) {
                default:
                    $titles = array(
                        "Компания",
                        "Участники",
                        "Счет",
                        "Комментарий",
                    );
                    break;
            }

            if ($print) {
                return $this->render('admin/download/print.html.twig', array(
                    'conf' => $conf,
                    'titles' => $titles,
                    'orgs' => $orgs,
                    'woinvoice' => $woinvoice,
                    'wopaid' => $wopaid,
                ));
            } else {
                $content = implode(';', $titles) . "\r\n";
                $file = 'uploads/' . $format . '/' . $filename . '.' . $format;
                file_put_contents($file, "");
                /** @var Organization $org */
                foreach ($orgs as $org) {
                    if (!empty($org->getUsers())) {
                        // Компания
                        $content .= str_replace("\r\n", "", str_replace(";", "", str_replace('"', "", $org->getName()))) . ";";
                        // Участники
                        $members = $org->getUsers();
                        $content .= '"';
                        $mi = 1;
                        /** @var User $member */
                        foreach ($members as $member) {
                            if (count($member->getUtoas()) > 0) {
                                $ap = '';
                                /** @var UserToApartament $utas */
                                foreach ($member->getUtoas() as $utas) {
                                    $apid = $utas->getApartament();
                                    /** @var Apartament $apc */
                                    $apc = $apid->getApartament();
                                    $ap .= '(' . $apc->getTitle() . ' - ' . $apc->getPrice() . ')';
                                }
                                $content .= $mi++ . ". " . $member->getLastName() . " " . $member->getFirstName() . " " . $member->getMiddleName() . " " . $ap . "\r\n";
                            }
                        }
                        $content .= '";';
                        // № Счета
                        /** @var OrgToConf $otc */
                        $otcs = $org->getOtc();
                        foreach ($otcs as $otc) {
                            if ($otc->getConferenceId() == $conf->getId()) {
                                $content .= '"№ ' . str_replace("\r\n", "", str_replace(";", "", str_replace('"', "", $otc->getInvoice()))) . "\r\n";
                                $content .= str_replace("\r\n", "", str_replace(";", "", str_replace('"', "", $otc->getPaymentDate() != null ? $otc->getPaymentDate()->format('Y') == date('Y') ? $otc->getPaymentDate()->format('Y-m-d H:i:s') : null : null))) . "\r\n";
                                // Оплата
                                $content .= (($otc->getPaid() == 2) ? "Частично оплачено" : ($otc->getPaid() == 0) ? "Не оплачено" : "Оплачено") . "\r\n" . str_replace("\r\n", "", str_replace(";", "", str_replace('"', "", ($otc->getPaidSum() == null) ? 0 : $otc->getPaidSum() . "/" . $otc->getSumm())));
                                $content .= '";' . "\r\n";
                                break;
                            }
                        }
                        // Реквизиты
                        //$content .= '"ИНН: '.$org->getInn()."\r\n";
                        //$content .= 'КПП: '.$org->getKpp()."\r\n";
                        //$content .= str_replace(";", "", str_replace('"', "", $org->getRequisites())) .'"'. ";\r\n";
                    }
                }

                file_put_contents($file, $content, FILE_APPEND);

                if (file_exists($file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename=' . basename($file));
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file));
                    readfile($file);
                    exit();
                }
                return $this->redirectToRoute("downloads");
            }
        } elseif ($filename == 'apartament') {

            /** @var ApartamentPairRepository $ApartamentPairRepository */
            $ApartamentPairRepository = $this->getDoctrine()
                ->getRepository('App:ApartamentPair');

            /** @var ApartamentPair $pairs */
            $pairs = $ApartamentPairRepository->findFull($conf->getId());

            return $this->render('admin/download/print_apartament.html.twig', array(
                'pairs' => $pairs,
                'year' => $year,
            ));
        } elseif ($filename == 'post') {
            /** @var UserToConfRepository $UserToConfRepository */
            $UserToConfRepository = $this->getDoctrine()
                ->getRepository('App:UserToConf');
            /** @var UserToConf $users */
            $userstoconf = $UserToConfRepository->findByConfWithPost($conf->getId());

            return $this->render('admin/download/print_post.html.twig', array(
                'userstoconf' => $userstoconf,
            ));
        } elseif ($filename == 'forexcel') {
            /** @var UserToConfRepository $UserToConfRepository */
            $UserToConfRepository = $this->getDoctrine()
                ->getRepository('App:UserToConf');
            /** @var UserToConf $users */
            $userstoconf = $UserToConfRepository->findByConfWithPost($conf->getId());

            if (!$print) {
                $response =  $this->render('admin/download/csv_forexcel.html.twig', array(
                    'userstoconf' => $userstoconf,
                ));

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=members.csv');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Type: text/html; charset=windows-1251');

                $response->setCharset("WINDOWS-1251");
                $response->setContent( mb_convert_encoding( $response->getContent() , "WINDOWS-1251",  "UTF-8" ));

                return $response;
            }

            return $this->render('admin/download/print_forexcel.html.twig', array(
                'userstoconf' => $userstoconf,
            ));
        } elseif($filename == 'woman'){
            /** @var UserRepository $UserRepository */
            $UserRepository = $this->getDoctrine()
                ->getRepository('App:User');
            /** @var User $users */
            $users = $UserRepository->findGender('female');
            return $this->render('admin/download/print_woman.html.twig', array(
                'users' => $users,
            ));
        } elseif($filename == 'corpus'){
            /** @var CorpusesRepository $corpusesRepository */
            $corpusesRepository = $this->getDoctrine()->getRepository('App:Corpuses');
            /** @var Corpuses $corpuses */
            $corpuses = $corpusesRepository->findAll();

            return $this->render('admin/download/print_corpus.html.twig', array(
                'year' => $year,
                'corpuses' => $corpuses,
            ));
        } elseif($filename == 'security') {

            /** @var UserToConfRepository $UserToConfRepository */
            $UserToConfRepository = $this->getDoctrine()
                ->getRepository('App:UserToConf');
            /** @var UserToConf $users */
            $userstoconf = $UserToConfRepository->findByConfWithPost($conf->getId(), 'u.lastName, u.firstName');
            if (!$print) {
                $response =  $this->render('admin/download/csv_security.html.twig', array(
                    'userstoconf' => $userstoconf,
                ));

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=security.csv');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Type: text/html; charset=windows-1251');

                $response->setCharset("WINDOWS-1251");
                $response->setContent( mb_convert_encoding( $response->getContent() , "WINDOWS-1251",  "UTF-8" ));

                return $response;
            }

            return $this->render('admin/download/print_security.html.twig', array(
                'userstoconf' => $userstoconf,
            ));
        }elseif($filename == 'interview'){
            /** @var InterviewRepository $interviewRepository */
            $interviewRepository = $this->getDoctrine()->getRepository('App:Interview');
            /** @var OrganizationRepository $orgsts */
            $orgsts = $this->getDoctrine()->getRepository('App:Organization');
            /** @var Interview $interview */
            $interview = $interviewRepository->findAll();
            $response =  $this->render('admin/download/csv_interview.html.twig', array(
                'data' => $interview,
            ));
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=interview.csv');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            $response->headers->set('Content-Type', 'text/csv');
            $response->setCharset("WINDOWS-1251");
            $response->setContent( mb_convert_encoding( $response->getContent() , "WINDOWS-1251",  "UTF-8" ));

            return $response;
        }
        elseif($filename == 'hotel'){
            /** @var CorpusesRepository $corpusesRepository */
            $corpusesRepository = $this->getDoctrine()->getRepository('App:Corpuses');
            /** @var Corpuses $corpuses */
            $corpuses = $corpusesRepository->findAll();

            /** @var ApartamentPairRepository $ApartamentPairRepository */
            $ApartamentPairRepository = $this->getDoctrine()
                ->getRepository('App:ApartamentPair');

            /** @var ApartamentPair $pairs */
            $pairs = $ApartamentPairRepository->findFull($conf->getId(), true);

            return $this->render('admin/download/print_hotel.html.twig', array(
                'year' => $year,
                'corpuses' => $corpuses,
                'pairs' => $pairs,
            ));

        } elseif($filename == 'hotel_2018'){
            if (!$print) {
                /** @var array $data */
                $data = $this->__getDataToHotel();

                $response = $this->render('admin/download/csv_hotel_2018.html.twig', array(
                    'data' => $data,
                ));

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=hotel.csv');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Type: text/html; charset=windows-1251');

                $response->setCharset("WINDOWS-1251");
                $response->setContent( mb_convert_encoding( $response->getContent() , "WINDOWS-1251",  "UTF-8" ));

                return $response;
            }

            /** @var array $data */
            $data = $this->__getDataToHotel();

            return $this->render('admin/download/csv_hotel_2018.html.twig', array(
                'data' => $data,
            ));
        } else {
            $response = new Response('Не удалось получить данные');
            return $response;
        }
    }


    /**
     * Генератор пароля
     *
     * @Route("/admin/encode/{pas}", name="admin-encode")
     * @Route("/admin/encode")
     *
     * @param int $pas
     * @return object
     */
    public function encode($pas = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($pas != null) {
            $user = $this->getUser();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $pas);
            echo 'decoded: ' . $pas . ' encoded ' . $encoded;
        } else {
            $users = $this->getDoctrine()
                ->getRepository('App:User')
                ->findBy(array('roles' => '["ROLE_MANAGER"]'));

            /** @var User $user */
            foreach ($users as $user) {
                if ($user->getPassword() == 'not') {
                    $password = substr(md5($user->getLastName() . $user->getFirstName()), 0, 6);
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password);
                    $user->setPassword($encoded);

                    $em->persist($user);
                    $em->flush();

                    echo $user->getLastName() . ' ' . $user->getFirstName() . '<br>Login ' . $user->getEmail() . '<br>Пароль ' . $password . '<br><hr></br>';
                }
            }
        }
        die();
    }

    /**
     * SQL select data to hotel CROS-2018
     *
     * Get result array of SQL query
     *
     * @return array
     */
    private function __getDataToHotel()
    {
        /** @var ManagerRegistry $em */
        $em = $this->getDoctrine()->getManager();

        $RAW = "
          CREATE TEMPORARY TABLE tmp_corp AS
              SELECT id, stage1 AS stage
              FROM corpuses
              UNION
              SELECT id, stage2
              FROM corpuses
              UNION
              SELECT id, stage3
              FROM corpuses
            ;
            CREATE TEMPORARY TABLE tmp_stage AS
              SELECT id, flat1 AS flat
              FROM stage
              WHERE flat1 IS NOT NULL
              UNION
              SELECT id, flat2
              FROM stage
              WHERE flat2 IS NOT NULL
              UNION
              SELECT id, flat3
              FROM stage WHERE flat3 IS NOT NULL
              UNION
              SELECT id, flat4
              FROM stage WHERE flat4 IS NOT NULL
            ;
            CREATE TEMPORARY TABLE tmp_flat AS
              SELECT id, room1 AS room, real_id
              FROM flat
              WHERE room1 IS NOT NULL
              UNION
              SELECT id, room2 AS room, real_id
              FROM flat
              WHERE room2 IS NOT NULL
              UNION
              SELECT id, room3 AS room, real_id
              FROM flat
              WHERE room3 IS NOT NULL
              UNION
              SELECT id, room4 AS room, real_id
              FROM flat
              WHERE room4 IS NOT NULL
              UNION
              SELECT id, room5 AS room, real_id
              FROM flat
              WHERE room5 IS NOT NULL
            ;
            
            SELECT
              corp.id as corpus,
              flat.real_id AS room_num,
              type.title AS class,
              CONCAT_WS(
                  ' ',
                  user.last_name,
                  user.first_name,
                  user.middle_name
              ) AS member,
              org.name
            FROM tmp_corp corp
              INNER JOIN tmp_stage          stage ON corp.stage = stage.id
              INNER JOIN tmp_flat           flat  ON stage.flat = flat.id
              INNER JOIN apartament_id      apart ON flat.room = apart.id
              INNER JOIN apartament         type  ON apart.apartament_id = type.id
              INNER JOIN user_to_apartament uta   ON apart.id = uta.apartaments_id
              INNER JOIN user               user  ON uta.user_id = user.id
              INNER JOIN organization       org   ON user.organization_id = org.id
            ORDER BY room_num ASC
            ;
            
            DROP TABLE tmp_corp;
            DROP TABLE tmp_stage;
            DROP TABLE tmp_flat;
            ";

        /** @var Statement $statement */
        $statement = $em->getConnection()->prepare($RAW);

        return $statement->fetchAll();
    }
}
