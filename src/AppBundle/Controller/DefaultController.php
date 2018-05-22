<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use AppBundle\Entity\Interview;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }
    /**
     * @Route("/thankyou", name="end-interview")
     * @param Request $request
     * @return object
     */
    public function interviewend()
    {
        return $this->render('interview/interviewend.twig',array());
    }
    /**
     * @Route("/oops", name="error-interview")
     * @param Request $request
     * @return object
     */
    public function interviewerr()
    {
        return $this->render('interview/interviewerr.twig',array());
    }
    /**
     * @Route("/interview", name="interview")
     * @param Request $request
     * @return object
     */
    public function interview(Request $request)
    {
        $orgsts = $this->getDoctrine()->getRepository('AppBundle:Organization');
        $orgsts = $orgsts->findBy(array(), array('id' => 'ASC'));
        foreach ($orgsts as $org){
            $boxOrgsts[$org->getName()] = $org->getId();
        }
        $form = $this->createFormBuilder()
            ->add('organization', ChoiceType::class, array(
                'label' => 'Организация',
                'choices'  => $boxOrgsts))
            ->add('name', TextType::class, array('label' => '2. Ваше имя'))
            ->add('visits', ChoiceType::class, array(
                'label' => '3. Сколько раз Вы посещали КРОС?',
                'choices'  => array(
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5',
                    '6'=>'6',
                    '7'=>'7',
                    '8'=>'8',
                    '9'=>'9',
                    '10'=>'10',
                    '11'=>'11',
                    '12'=>'12',
                    '13'=>'13',
                )))
            ->add('QualityOrganization', ChoiceType::class, array(
                'label' => '4. Оцените, пожалуйста, качество организации и проведения КРОС 2.0-18 в целом.',
                'choices'  => array(
                    'Есть над чем работать'=>'1',
                    'Нормально'=>'2',
                    'Хорошо'=>'3',
                    'На высшем уровне'=>'4',
                )))
            ->add('QualityOrganizationComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
                ))
            ->add('Presentations',ChoiceType::class,
                array(
                    'label' => '5. Оцените, пожалуйста, актуальность и качество подготовки презентаций/докладов',
                    'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                     '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('PresentationsComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий, можно описать свои впечатления, в том числе, и по сравнению с прошлыми годами. ',
            ))
            ->add('tables',ChoiceType::class,
                array(
                    'label' => '6. Оцените, пожалуйста, актуальность и качество проведения круглых столов',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('tablesComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('Entertainment',ChoiceType::class,
                array(
                    'label' => '7. Оцените развлекательную часть мероприятия',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('EntertainmentComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Какие развлечения вы хотели бы увидеть в следующем году?',
            ))
            ->add('Food', ChoiceType::class, array(
                'label' => '8. Оцените, пожалуйста, качество организации питания: кухню и сервис',
                'choices'  => array(
                    'Организация питания оставляет желать лучшего'=>'Организация питания оставляет желать лучшего',
                    'Нормально, но время организовано неудачно'=>'Нормально, но время организовано неудачно',
                    'Хорошая кухня, удобный график'=>'Хорошая кухня, удобный график',
                    'Питание организовано идеально, ничего менять не требуется'=>'Питание организовано идеально, ничего менять не требуется',
                )))
            ->add('FoodComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('Search',ChoiceType::class,
                array(
                    'label' => '9. Оцените информационное сопровождение конференции: удобство и своевременность получения информации, возможность связаться с коллегами, подсказки об изменении в расписании. В комментариях можно предложить варианты улучшения информационного обмена.',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('SearchComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('WhatImportantComent', TextareaType::class, array(
                'required' => false,
                'label' => '10. Чего вам не хватило, чтобы ощутить себя на идеально организованной провайдерской конференции?',
            ))
            ->add('save', SubmitType::class,array('label' => 'Отправить ответы') )
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $Interview = new Interview();
            $em = $this->getDoctrine()->getManager();
            $form = $form->getData();

            $Interview->setName($form['name']);
            if($form['organization'] != null) {
                $orgsts = $this->getDoctrine()->getRepository('AppBundle:Organization');
                $organization = $orgsts->find($form['organization']);
                $Interview->setCompany($organization);
            }else{
                return $this->redirectToRoute('error-interview');
            }
            $Interview->setVisits($form['visits']);
            $Interview->setQualityOrganization($form['QualityOrganization']);
            //$Interview->setQualityOrganizationComents($form['QualityOrganizationComents']);
            $Interview->setPresentations($form['Presentations']);
            $Interview->setTables($form['tables']);
            $Interview->setEntertainment($form['Entertainment']);
            $Interview->setFood($form['Food']);
            $Interview->setSearch($form['Search']);
            //$Interview->setInformationalResources($form['InformationalResources']);
            //$Interview->setWhatImportant(1);

            if($form['QualityOrganizationComents'] != null){
                $Interview->setQualityOrganizationComents($form['QualityOrganizationComents']);
            }
            if($form['PresentationsComents'] != null){
                $Interview->setPresentationsComents($form['PresentationsComents']);
            }
            if($form['tablesComents'] != null){
                $Interview->setTablesComents($form['tablesComents']);
            }
            if($form['EntertainmentComents'] != null){
                $Interview->setEntertainmentComents($form['EntertainmentComents']);
            }
            if($form['FoodComents'] != null){
                $Interview->setFoodComents($form['FoodComents']);
            }
            if($form['SearchComents'] != null){
                $Interview->setSearchComents($form['SearchComents']);
            }
            /*
            if($form['InformationalResourcesComents'] != null){
                $Interview->setInformationalResourcesComents($form['InformationalResourcesComents']);
            }
            */
            if($form['WhatImportantComent'] != null){
                $Interview->setWhatImportantComent($form['WhatImportantComent']);
            }
            /*
            if($form['WhatImportant'] != null){
                $shops = implode(';', $form['WhatImportant']);
                $Interview->setWhatImportant($shops);
            }
            */
            $em->persist($Interview);
            $em->flush();

            return $this->redirectToRoute('end-interview');
        }

        return $this->render('interview/interview.twig',array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/", name="homepage")
     */
    public function newMainAction()
    {
        $year = date("Y");
        $reg_time = $this->getDoctrine()->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date("Y")));
        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $this->getDoctrine()->getRepository('AppBundle:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(array('year' => $year));

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        /** @var Speaker $speakers */
        $speakers = $speakerRepository->findByConf($conf->getId());
        $rand_keys = array_rand($speakers, 4);
        foreach ($rand_keys as $val) {
            $speakers_rand[] = $speakers[$val];
        }
        /*
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        $speaker = $speakerRepository->findBy(array('conferenceId' => $reg_time->getId()));
        */
        $speakerList = NULL;
        foreach ($speakers_rand as $key =>  $value){
            $speakerList[$key]['id'] = $value->getid();
            $speakerList[$key]['AvatarSmall'] = $value->getAvatarSmall();
            $speakerList[$key]['Organization'] = $value->getUser()->getOrganization()->getName();
            $speakerList[$key]['SpeakerFirstName'] = $value->getUser()->getFirstName();
            $speakerList[$key]['SpeakerLastName'] = $value->getUser()->getLastName();
            $speakerList[$key]['SpeakerMiddleName'] = $value->getUser()->getMiddleName();
        }
        $reg_start = $reg_time->getRegistrationStart()->getTimestamp();
        return $this->render('cros2/main/base.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'reg_start' => $reg_start,
            'speaker_list' => $speakerList,


        ));
    }

    /**
     * @Route("/old", name="cros-old")
     */
    public function indexAction()
    {
        $monthes = array(
            '01' => 'января',
            '02' => 'февраля',
            '03' => 'марта',
            '04' => 'апреля',
            '05' => 'мая',
            '06' => 'июня',
            '07' => 'июля',
            '08' => 'августа',
            '09' => 'сентября',
            '10' => 'октября',
            '11' => 'ноября',
            '12' => 'декабря'
        );

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date('Y')));
        $event_date = $conf->getStart()->format('m');
        $sday = $conf->getStart()->format('d');
        $eday = $conf->getFinish()->format('d');
        $year= $conf->getStart()->format('Y');
        $month = $monthes[$event_date];

        return $this->render('default/door.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'month' => $month,
            'sday' => $sday,
            'eday' => $eday,
            'year' => $year,
        ));
    }

    /**
     * @Route("/footer", name="footer")
     */
    public function footerAction()
    {
        /** @var Setting $settings */
        $settings = $this->getDoctrine()
            ->getRepository('AppBundle:Setting')
            ->find(1);

        $footer_text = $settings->getFooterText();

        return $this->render('default/footer.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'footer_text' => $footer_text,
        ));
    }

    /**
     */
    public function countdownAction()
    {
        $reg_time = $this->getDoctrine()->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date("Y")));

        $reg_start = $reg_time->getRegistrationStart()->getTimestamp();
        $reg_finish = $reg_time->getRegistrationFinish()->getTimestamp();
        $now = time();

        $countdown_date = false;
        $text = false;
        if ($now < $reg_start) {
            // before reg
            $countdown_date = $reg_start;
            $text = "До начала регистрации";
        } elseif ($now > $reg_start && $now < $reg_finish) {
            // regtime
            $countdown_date = $reg_finish;
            $text = "До конца регистрации";
        } elseif ($now > $reg_finish) {
            // after reg
            $text = "Регистрация окончена";
        };

        return $this->render('default/countdown.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'countdown_date' => $countdown_date,
            'text' => $text
        ));
    }

    /**
     */
    public function newcountdownAction($isMainPage = false)
    {
        $countdown_date = new \DateTime("1970-01-01 00:00");
        $now = new \DateTime('now');

        /** @var Conference $conf */
        $conf = $this->getDoctrine()->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date("Y")));

        $reg_start = $conf->getRegistrationStart();
        $event_start = $conf->getStart();

        if ($now < $event_start) {
            $countdown_date = $event_start;
        };

        return $this->render('cros2/misc/_countdown.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'countdown_date' => $countdown_date,
            'countdown_text' => 'До начала мероприятия',
            'main_page' => $isMainPage
        ));
    }

    public function viewSpeakers()
    {
        $countdown_date = 'value';

    }
}
