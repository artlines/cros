<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\ManagerGroup;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class RegistrationController extends Controller
{
    /**
     * @Route("/registration/{man_hash}", name="registration")
     * @Route("/registration")
     */
    public function registrationAction($man_hash = null, Request $request)
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            if($this->get('security.authorization_checker')->isGranted('ROLE_ORG')) {
                // Если пользователь авторизован - переадресуем в личный кабинет
                return $this->redirectToRoute('profile');
            }
            else{
                return $this->render('frontend/registration/authorized.html.twig', array(
                    'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                ));
            }
        }
        else {
            // Регистрация
            $man_id = null;

            $isset_manager = false;

            $year = date("Y");

            /** @var Conference $conf */
            $conf = $this->getDoctrine()
                ->getRepository('AppBundle:Conference')
                ->findOneBy(array('year' => $year));

            /** @var User $users_yet */
            $users_yet = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findByConf($conf->getId());
            $uc = count($users_yet);

            // Получаем разрешенные даты регистрации
            $reg_start = $conf->getRegistrationStart();
            $reg_finish = $conf->getRegistrationFinish();

            $now = date('Y-m-d H:i:s');

            // Проверяем, есть ли такой менеджер
            /** @var ManagerGroup $managers */
            $manager = $this->getDoctrine()
                ->getRepository('AppBundle:ManagerGroup')
                ->findOneBy(array('hash' => $man_hash));
            if($manager != null){
                $isset_manager = true;
                $man_id = $manager->getId();
            }

            // Проверяем, открыта ли регистрация или пользователь регистрируется по ссылке менеджера
            if(($reg_start->format('Y-m-d H:i:s') <= $now && $reg_finish->format('Y-m-d H:i:s') >= $now || ($man_hash != null && $isset_manager)) /*&& $uc < 559*/ ) {

                /** @var Organization $org */
                $org = new Organization();
                /** @var Form $form */
                $form = $this->createFormBuilder($org)
                    ->add('name', TextType::class, array('label' => 'Название организации', 'attr' => array('placeholder' => 'Ёлки-телеком', 'data-helper' => 'Ваш основной Торговый знак, будет использоваться на бейджах и визитках')))
                    ->add('city', TextType::class, array('label' => 'City'))
                    ->add('email', EmailType::class, array('label' => 'E-mail', 'required' => true, 'attr' => array('data-helper' => 'Для общих уведомлений, будет использоваться в качестве логина для доступа в личный кабинет')))
                    ->add('email_confirm', EmailType::class, array('label' => 'Подтверждение E-mail', 'required' => true, 'mapped' => false))
                    ->add('username', TextType::class, array('label' => 'Телефон', 'attr' => array('data-helper' => 'Общий телефон для связи с Компанией', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
                    ->add('inn', TextType::class, array('label' => 'ИНН', 'required' => true))
                    ->add('kpp', TextType::class, array('label' => 'КПП', 'required' => true))
                    ->add('requisites', TextareaType::class, array('label' => 'Реквизиты', 'attr' => array('data-helper' => 'Для выставления счета')))
                    ->add('address', TextareaType::class, array('label' => 'Address', 'required' => false))
                    ->add('comment', TextareaType::class, array('label' => 'Комментарий', 'required' => false))
                    ->add('manager', HiddenType::class, array('label' => 'Manager', 'mapped' => false, 'required' => false, 'data' => $man_id))
                    ->add('save', SubmitType::class, array('label' => 'Продолжить'))
                    ->getForm();

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {


                    /** @var Organization $org */
                    $org = $form->getData();

                    if($org->getEmail() != $form->get('email_confirm')->getData()){
                        $result = array(
                            'element' => 'email_confirm',
                            'status' => 'danger',
                            'text' => 'E-mail адреса не совпадают!',
                        );
                        return $this->render('frontend/registration/registration.html.twig', array(
                            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                            'form' => $form->createView(),
                            'result' => $result,
                        ));
                    }

                    /** @var Organization $check_org */
                    $check_org = $this->getDoctrine()
                        ->getRepository('AppBundle:Organization')
                        ->findOneBy(array('email' => $org->getEmail()));

                    if($check_org != null){
                        $what_exist = 'На этот e-mail уже зарегистрирована организация';
                        $result = array(
                            'element' => 'email',
                            'status' => 'danger',
                            'text' => $what_exist,
                        );
                        return $this->render('frontend/registration/registration.html.twig', array(
                            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                            'form' => $form->createView(),
                            'result' => $result,
                        ));
                    }
                    else{
                        /** @var Organization $check_org */
                        $check_org = $this->getDoctrine()
                            ->getRepository('AppBundle:Organization')
                            ->findOneBy(array('username' => $org->getUsername()));

                        if($check_org != null){
                            $what_exist = 'На этот телефон уже зарегистрирована организация';
                            $result = array(
                                'element' => 'username',
                                'status' => 'danger',
                                'text' => $what_exist,
                            );
                            return $this->render('frontend/registration/registration.html.twig', array(
                                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                                'form' => $form->createView(),
                                'result' => $result,
                            ));
                        }
                    }

                    $org->setIsActive(1);
                    $org->setSponsor(false);

                    $status = $this->getDoctrine()
                        ->getRepository('AppBundle:OrganizationStatus')
                        ->find(1);

                    $manager_id = $form->get('manager')->getData();
                    if(!$manager_id){
                        $managers = null;
                    }
                    else {
                        $managers = $this->getDoctrine()
                            ->getRepository('AppBundle:ManagerGroup')
                            ->find($manager_id);
                    }

                    $org->setStatus(1);
                    $org->setTxtstatus($status);
                    $org->setManagers($managers);

                    $manager = $form->get('manager')->getData();
                    $org->setManager($manager);

                    $password = substr(md5($org->getName()), 0, 6);
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($org, $password);

                    $org->setPassword($encoded);

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($org);
                    $em->flush();

                    $otc = new OrgToConf();
                    $otc->setOrganizationId($org->getId());
                    $otc->setConferenceId($conf->getId());
                    $otc->setPaid(0);
                    $otc->setOrganization($org);
                    $otc->setConference($conf);

                    $em->persist($otc);
                    $em->flush();


                    $event = new Logs();
                    $event->setEntity('organization');
                    $event->setEvent('Зарегистрирована новая компания');
                    $event->setElementId($org->getId());
                    $event->setReaded(0);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Регистрация КРОС-2.0-18: '.$org->getName().' Доступ в личный кабинет')
                        ->setFrom('cros@nag.ru')
                        ->setTo($org->getEmail())
                        ->setBcc($this->container->getParameter('cros_emails'))
                        ->setBody(
                            $this->renderView(
                                'Emails/org_registration.html.twig',
                                array(
                                    'email' => $org->getEmail(),
                                    'password' => $password,
                                    'org' => $org->getName(),
                                )
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);

                    /*$event = new Logs();
                    $event->setEntity('organization');
                    $event->setEvent('Зарегистрирована новая организация');
                    $event->setElementId($org->getId());
                    $event->setReaded(0);

                    $em->persist($event);
                    $em->flush();*/
                    /*
                    $token = new UsernamePasswordToken($org, $org->getPassword(), 'default', $org->getRoles());
                    $securityContext = $this->container->get('security.token_storage');
                    $securityContext->setToken($token);*/

                    $result = array(
                        'status' => 'success',
                        'text' => 'Сохранено',
                    );
                    //return $this->redirectToRoute('registration-2');
                    return $this->render('frontend/registration/registration_success.html.twig', array(
                        'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                        'company' => $org->getName(),
                        'email' => $org->getEmail()
                    ));

                }
                return $this->render('frontend/registration/registration.html.twig', array(
                    'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                    'form' => $form->createView(),
                    'manager' => $man_id,
                ));
            }
            return $this->render('frontend/registration/registration_closed.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..') . DIRECTORY_SEPARATOR,
                'conf' => $conf,
            ));
        }
    }

    /**
     * @Route("/registration2/{id}", name="registration-2")
     * @Route("/registration2")
     *
     * @param integer|null $id
     * @param Request $request
     *
     * @return object
     */
    public function stepTwoAction($id = null, Request $request){
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            // Пользователь зарегистрировался и успешно авторизовался

            $year = date("Y");

            $upd = false;

            $apartament_id = null;

            $em = $this->getDoctrine()->getManager();

            /** @var Conference $conf */
            $conf = $this->getDoctrine()
                ->getRepository('AppBundle:Conference')
                ->findOneBy(array('year' => date("Y") /*$year*/ ));

            /** @var Apartament $apartaments */
            $apartaments = $this->getDoctrine()
                ->getRepository('AppBundle:Apartament')
                ->findBy(array('conferenceId' => $conf->getId()));

            $numbers = array('Выберите номер проживания' => '');
            $numberdesc = array();

            /** @var Organization $gorg */
            $gorg = $this->getDoctrine()
                ->getRepository('AppBundle:Organization')
                ->find($this->getUser()->getId());

            $ou = count($gorg->getUsers()) + 1;

            if($id != null){
                /** @var User $user */
                $user = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->findOneBy(array('id' => $id, 'organizationId' => $this->getUser()->getId()));
                if($user == null){
                    $user = new User();

                    /** @var User $users_yet */
                    $users_yet = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->findAll();
                    $uc = count($users_yet);

                    if($uc >= 558){
                        return $this->redirectToRoute('registration-3');
                    }
                }
                else{
                    /** @var UserToApartament $utac*/
                    $utac = $this->getDoctrine()
                        ->getRepository('AppBundle:UserToApartament')
                        ->findOneBy(array('userId' => $user->getId()));
                    if($utac != null) {
                        /** @var ApartamentId $apid */
                        $apid = $utac->getApartament();
                        $apartament_id = $apid->getApartamentId();
                    }
                    $upd = $user;
                }
            }
            else{
                $user = new User();

                $user->setArrival(new \DateTime('14:00 16.05.2018'));
                $user->setLeaving(new \DateTime('12:00 19.05.2018'));

                /** @var User $users_yet */
                $users_yet = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->findAll();
                $uc = count($users_yet);

                if($uc >= 558){
                    return $this->redirectToRoute('registration-3');
                }
            }


            /** @var Apartament $apartament */
            foreach ($apartaments as $apartament){
                $freenum = 0;
                $rfn = 0;
                $partialfree = 0;
                $apids = $apartament->getAitoas();
                /** @var ApartamentId $apid */
                foreach ($apids as $apid){
                    $useraps = $apid->getAtoais();
                    $usap = 0;
                    foreach($useraps as $userap){
                        $usap++;
                    }
                    if($usap === 0){
                        $freenum += $apartament->getPlaces();
                    }
                    elseif($usap < $apartament->getPlaces()){
                        $freenum++;
                    }
                    if(count($useraps) == 0){
                        $rfn++;
                    }
                }/*
                if($apartament->getCode() == 'std single'){
                    $freenum--;
                }*/
                if($freenum > 0 || $apartament_id == $apartament->getId()) {
                    if($upd){
                        if($apartament_id == $apartament->getId()){
                            $numbers[$apartament->getTitle() . ' (' . $apartament->getPlaces() . '-местный номер, ' . $apartament->getPrice() . 'р. за человека)'] = $apartament->getId();
                            $numberdesc[$apartament->getId()] = $apartament->getDescription();
                        }
                    }
                    else {
                        $numbers[$apartament->getTitle() . ' (' . $apartament->getPlaces() . '-местный номер, ' . $apartament->getPrice() . 'р. за человека) - свободно мест: ' . $freenum . ', пустых номеров: ' . $rfn] = $apartament->getId();
                        $numberdesc[$apartament->getId()] = $apartament->getDescription();
                    }
                }
            }
            $class_help = '';

            if($upd){
                $class_help = 'Для смены класса участия, напишите на cros@nag.ru';
            }

            if($ou > 1) {
                /** @var Form $form */
                $form = $this->createFormBuilder($user)
                    ->add('last_name', TextType::class, array('label' => 'Last name'))
                    ->add('first_name', TextType::class, array('label' => 'First name'))
                    ->add('middle_name', TextType::class, array('label' => 'Middle name', 'required' => false))
                    ->add('post', TextType::class, array('label' => 'Post', 'required' => true))
                    ->add('email', EmailType::class, array('label' => 'E-mail'))
                    ->add('username', TextType::class, array('label' => 'Mobile phone', 'attr' => array('data-helper' => 'Телефон для связи', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
                    ->add('nickname', TextType::class, array('label' => 'Nickname', 'required' => false))
                    ->add('arrival', TimeType::class, array(
                        'label' => 'Ранний заезд',
                        'input' => 'datetime',
                    ))
                    ->add('leaving', TimeType::class, array(
                        'label' => 'Поздний выезд',
                        'input' => 'datetime',
                    ))
                    ->add('car_number', TextType::class, array('label' => 'Если Вы приедете на личном транспорте, укажите его государственный номер', 'required' => false, 'attr' => array('placeholder' => 'А001АА 00', 'pattern' => '[А-Яа-яA-Za-z]{1,1}[0-9]{3,3}[А-Яа-яA-Za-z]{2,2}[ ][0-9]{2,3}', 'title' => 'А001АА 00')))
                    ->add('apartament', ChoiceType::class, array('label' => 'Класс участия', 'mapped' => false, 'attr' => array('data-helper' => $class_help), 'choices' => $numbers, 'choice_attr' => array('Выберите номер проживания' => array('disabled' => '')), 'data' => $apartament_id))
                    //->add('save_and_add', SubmitType::class, array('label' => 'Сохранить и добавить еще одного'))
                    ->add('save', SubmitType::class, array('label' => 'Продолжить'))
                    //->add('continue', SubmitType::class, array('label' => 'Завершить без сохранения'))
                    ->getForm();
            }
            else{
                /** @var Form $form */
                $form = $this->createFormBuilder($user)
                    ->add('last_name', TextType::class, array('label' => 'Last name'))
                    ->add('first_name', TextType::class, array('label' => 'First name'))
                    ->add('middle_name', TextType::class, array('label' => 'Middle name', 'required' => false))
                    ->add('post', TextType::class, array('label' => 'Post', 'required' => true))
                    ->add('email', EmailType::class, array('label' => 'E-mail'))
                    ->add('username', TextType::class, array('label' => 'Mobile phone', 'attr' => array('data-helper' => 'Телефон для связи', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
                    ->add('nickname', TextType::class, array('label' => 'Nickname', 'required' => false))
                    ->add('arrival', TimeType::class, array(
                        'label' => 'Ранний заезд',
                        'input' => 'datetime',
                    ))
                    ->add('leaving', TimeType::class, array(
                        'label' => 'Поздний выезд',
                        'input' => 'datetime',
                    ))
                    ->add('car_number', TextType::class, array('label' => 'Если Вы приедете на личном транспорте, укажите его государственный номер', 'required' => false, 'attr' => array('placeholder' => 'А001АА 00', 'pattern' => '[А-Яа-яA-Za-z]{1,1}[0-9]{3,3}[А-Яа-яA-Za-z]{2,2}[ ][0-9]{2,3}', 'title' => 'А001АА 00')))
                    ->add('apartament', ChoiceType::class, array('label' => 'Класс участия', 'mapped' => false, 'choices' => $numbers, 'choice_attr' => array('Выберите номер проживания' => array('disabled' => '')), 'data' => $apartament_id))
                    //->add('save_and_add', SubmitType::class, array('label' => 'Сохранить и добавить еще одного'))
                    ->add('save', SubmitType::class, array('label' => 'Продолжить'))
                    ->getForm();

            }


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var User $user */
                $user = $form->getData();

                if($request->query->has('continue')) {
                    if ($form->get('continue')->isClicked()) {
                        return $this->redirectToRoute('registration-3');
                    }
                }

                $change_log = array(
                    'fname' => false,
                    'lname' => false,
                    'mname' => false,
                    'post' => false,
                    'phone' => false,
                    'email' => false,
                    'nickname' => false,
                    'cnumber' => false,
                );

                /** @var User $check_usr */
                $check_usr = $this->getDoctrine()
                    ->getRepository('AppBundle:User')
                    ->findOneBy(array('email' => $user->getEmail()));

                if($check_usr != null && $check_usr->getEmail() != $user->getEmail()){
                    $what_exist = 'Пользователь с таким E-mail`ом уже существует';
                    $result = array(
                        'element' => 'email',
                        'status' => 'danger',
                        'text' => $what_exist,
                    );
                    return $this->render('frontend/registration/step_two.html.twig', array(
                        'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                        'form' => $form->createView(),
                        'result' => $result,
                        'counter' => $ou,
                        'upd' => $upd,
                        'numberdesc' => json_encode($numberdesc),
                    ));
                }
                else{
                    /** @var User $check_usr */
                    $check_usr = $this->getDoctrine()
                        ->getRepository('AppBundle:User')
                        ->findOneBy(array('username' => $user->getUsername()));

                    if($check_usr != null && $check_usr->getId() != $user->getId()){
                        $what_exist = 'Пользователь с таким телефоном уже существует';
                        $result = array(
                            'element' => 'username',
                            'status' => 'danger',
                            'text' => $what_exist,
                        );
                        return $this->render('frontend/registration/step_two.html.twig', array(
                            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                            'form' => $form->createView(),
                            'result' => $result,
                            'counter' => $ou,
                            'upd' => $upd,
                            'numberdesc' => json_encode($numberdesc),
                        ));
                    }
                }

                if(!$upd) {
                    $user->setPassword('not');
                    $user->setOrganizationId($this->getUser()->getId());
                    $user->setOrganization($this->getUser());
                    $user->setIsActive(1);
                    $user->setRoles(array('ROLE_USER'));
                }
                else{
                    $user->setSaved(0);
                }

                $em->persist($user);
                $em->flush();

                $apart_id = $form->get('apartament')->getData();

                if(!$upd) {
                    /** @var UserToConf $utc */
                    $utc = new UserToConf();
                    $utc->setUserId($user->getId());
                    $utc->setUser($user);
                    $utc->setConferenceId($conf->getId());

                    $em->persist($utc);
                    $em->flush();
                }

                /** @var Apartament $check_apart */
                $check_apart = $this->getDoctrine()
                    ->getRepository('AppBundle:Apartament')
                    ->find($apart_id);

                if($check_apart != null) {
                    $places = $check_apart->getPlaces();

                    /** @var ApartamentId $real_aparts */
                    $real_aparts = $this->getDoctrine()
                        ->getRepository('AppBundle:ApartamentId')
                        ->findBy(array('apartamentId' => $apart_id));

                    /** @var UserToApartament $chinaps */
                    $chinaps = $this->getDoctrine()
                        ->getRepository('AppBundle:UserToApartament')
                        ->findOneBy(array('userId' => $user->getId()));

                    if($chinaps != null) {
                        if ($chinaps->getApproved() == true) {
                            $aparts = $user->getUtoas();
                            /** @var UserToApartament $apart */
                            foreach($aparts as $apart){
                                /** @var ApartamentId $realapart */
                                $realapart = $apart->getApartament();
                                $realapart = $realapart->getApartament();
                                if($realapart->getConferenceId() == $conf->getId()){
                                    $urap = $chinaps->getApartament();
                                    if($urap->getId() != $realapart->getId()){
                                        $em->remove($chinaps);
                                        $em->flush();
                                        /** @var ApartamentId $real_apart */
                                        foreach ($real_aparts as $real_apart) {
                                            /** @var UserToApartament|array $utaf */
                                            $utaf = $this->getDoctrine()
                                                ->getRepository('AppBundle:UserToApartament')
                                                ->findBy(array('apartamentsId' => $real_apart->getId()));
                                            if (count($utaf) < $places) {
                                                $uta = new UserToApartament();
                                                $uta->setUserId($user->getId());
                                                $uta->setUser($user);
                                                $uta->setApartamentsId($real_apart->getId());
                                                $uta->setApartament($real_apart);
                                                $uta->setApproved(false);

                                                $em->persist($uta);
                                                $em->flush();
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $em->remove($chinaps);
                            $em->flush();
                            /** @var ApartamentId $real_apart */
                            foreach ($real_aparts as $real_apart) {
                                /** @var UserToApartament|array $utaf */
                                $utaf = $this->getDoctrine()
                                    ->getRepository('AppBundle:UserToApartament')
                                    ->findBy(array('apartamentsId' => $real_apart->getId()));
                                if (count($utaf) < $places) {
                                    $uta = new UserToApartament();
                                    $uta->setUserId($user->getId());
                                    $uta->setUser($user);
                                    $uta->setApartamentsId($real_apart->getId());
                                    $uta->setApartament($real_apart);
                                    $uta->setApproved(false);

                                    $em->persist($uta);
                                    $em->flush();
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        /** @var ApartamentId $real_apart */
                        foreach ($real_aparts as $real_apart) {
                            /** @var UserToApartament|array $utaf */
                            $utaf = $this->getDoctrine()
                                ->getRepository('AppBundle:UserToApartament')
                                ->findBy(array('apartamentsId' => $real_apart->getId()));
                            if (count($utaf) < $places) {
                                $uta = new UserToApartament();
                                $uta->setUserId($user->getId());
                                $uta->setUser($user);
                                $uta->setApartamentsId($real_apart->getId());
                                $uta->setApartament($real_apart);
                                $uta->setApproved(false);

                                $em->persist($uta);
                                $em->flush();
                                break;
                            }
                        }
                    }
                }
               /* $event = new Logs();
                $event->setEntity('user');
                $event->setEvent('Зарегистрирован новый участник');
                $event->setElementId($user->getId());
                $event->setReaded(0);

                $em->persist($event);
                $em->flush();*/

                $result = array(
                    'status' => 'success',
                    'text' => 'Сохранено',
                );

                if($form->get('save')->isClicked()){
                    return $this->redirectToRoute('registration-3');
                }
                else{
                    return $this->redirectToRoute('registration-2');
                }
            }

            return $this->render('frontend/registration/step_two.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'form' => $form->createView(),
                'counter' => $ou,
                'upd' => $upd,
                'numberdesc' => json_encode($numberdesc),
            ));
        }
        else{
            return $this->redirectToRoute('registration');
        }
    }

    /**
     * @Route("/registration3", name="registration-3")
     *
     * @return object
     */
    public function stepThreeAction(){
        $org = $this->getUser();

        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findBy(array('organizationId' => $org->getId(), 'isActive' => true));

        /** @var User $users_yet */
        $users_yet = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();
        $uc = count($users_yet);

        return $this->render('frontend/registration/step_three.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'org' => $org,
            'users' => $users,
            'useryet' => $uc,
        ));
    }

    /**
     * @Route("/registration_finish", name="registration-finish")
     */
    public function sendEmailsAndFinishAction(){

        $em = $this->getDoctrine()->getManager();

        /** @var Organization $org */
        $org = $this->getUser();
        $users = $org->getUsers();

        $sendall = false;

        /** @var User $user */
        foreach ($users as $user){
            if(!$user->getSaved()){
                $sendall = true;
                if($user->getPassword() != 'not') {
                    $full_name = $user->getLastName() . ' ' . $user->getFirstName() . ' ' . $user->getMiddleName();
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Регистрация КРОС-2.0-18: ' . $org->getName())
                        ->setFrom('cros@nag.ru')
                        ->setTo($user->getEmail())
                        ->setBcc($this->container->getParameter('cros_emails'))
                        ->setBody(
                            $this->renderView(
                                'Emails/edit_user.html.twig',
                                array(
                                    'fio' => $full_name,
                                    'phone' => $user->getUsername(),
                                    'org' => $this->getUser()->getName(),
                                    'email' => $user->getEmail(),
                                    'user' => $user,
                                    'arrival' => $user->getArrival()->format('H:i') == '14:00' ? 'нет' : $user->getArrival()->format('H:i'),
                                    'leaving' => $user->getLeaving()->format('H:i') == '12:00' ? 'нет' : $user->getLeaving()->format('H:i'),
                                )
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);
                    $user->setSaved(1);

                    $user->setChangeLog(null);

                    $em->persist($user);
                    $em->flush();
                }
                else{
                    $password = substr(md5($user->getLastName().$user->getFirstName()), 0, 6);
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $password);

                    $user->setPassword($encoded);
                    $user->setSaved(1);

                    $user->setChangeLog(null);

                    $em->persist($user);
                    $em->flush();

                    $full_name = $user->getLastName().' '.$user->getFirstName().' '.$user->getMiddleName();

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Регистрация КРОС-2.0-18: ' . $this->getUser()->getName())
                        ->setFrom('cros@nag.ru')
                        ->setTo($user->getEmail())
                        ->setBcc($this->container->getParameter('cros_emails'))
                        ->setBody(
                            $this->renderView(
                                'Emails/registration.html.twig',
                                array(
                                    'fio' => $full_name,
                                    'phone' => $user->getUsername(),
                                    'password' => $password,
                                    'org' => $this->getUser()->getName(),
                                    'email' => $user->getEmail(),
                                    'user' => $user,
                                    'arrival' => $user->getArrival()->format('H:i') == '14:00' ? 'нет' : $user->getArrival()->format('H:i'),
                                    'leaving' => $user->getLeaving()->format('H:i') == '12:00' ? 'нет' : $user->getLeaving()->format('H:i'),
                                )
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);
                }
            }
        }

        if($org->getManager() != null){
            /** @var User $managers_foremail */
            $managers_foremail = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->findBy(array('managerGroupId' => $org->getManager()));
            /** @var User $manager_foremail */
            foreach ($managers_foremail as $manager_foremail){
                if($manager_foremail->getManagerGroupId() != null){
                    $sysmail[] = $manager_foremail->getEmail();
                }
            }
        }

        if($sendall){
            $message = \Swift_Message::newInstance()
                ->setSubject('Регистрация КРОС-2.0-18: ' . $this->getUser()->getName())
                ->setFrom('cros@nag.ru')
                ->setTo($this->getUser()->getEmail())
                ->setBcc($this->container->getParameter('cros_emails'))
                ->setBody(
                    $this->renderView(
                        'Emails/all_registration.html.twig',
                        array(
                            'organization' => $this->getUser(),
                        )
                    ),
                    'text/html'
                );
            $this->get('mailer')->send($message);
        }

        return $this->render('frontend/registration/finish.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }
}
