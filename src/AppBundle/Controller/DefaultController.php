<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Setting;
use AppBundle\Entity\Speaker;
use AppBundle\Entity\Interview;
use AppBundle\Repository\ConferenceRepository;
use AppBundle\Repository\SpeakerRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
     * @Route("/rate", name="rate-form")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ratePage()
    {
        return $this->render('rate/index.html.twig');
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
                'label' => '4. Оцените, пожалуйста, качество организации и проведения КРОС 2.0-18 в целом',
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

        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $this->getDoctrine()->getRepository('AppBundle:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(['year' => $year]);

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        /** @var Speaker[] $speakers */
        $speakers = $speakerRepository->findByConf($conf->getId());

        $speakers_rand = [];
        if (count($speakers) > 1) {
            $rand_keys = array_rand($speakers, 4);
            foreach ($rand_keys as $val) {
                $speakers_rand[] = $speakers[$val];
            }
        }

        $speakerList = NULL;
        foreach ($speakers_rand as $key =>  $value){
            $speakerList[$key]['id'] = $value->getid();
            $speakerList[$key]['AvatarSmall'] = $value->getAvatarSmall();
            $speakerList[$key]['Organization'] = $value->getUser()->getOrganization()->getName();
            $speakerList[$key]['SpeakerFirstName'] = $value->getUser()->getFirstName();
            $speakerList[$key]['SpeakerLastName'] = $value->getUser()->getLastName();
            $speakerList[$key]['SpeakerMiddleName'] = $value->getUser()->getMiddleName();
        }

        return $this->render('cros2/main/base.html.twig', array(
            'base_dir'      => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'speaker_list'  => $speakerList,
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

    public function countdownAction($mainPage = false)
    {
        /** @var ConferenceRepository $confRepo */
        $confRepo = $this->getDoctrine()->getRepository('AppBundle:Conference');

        /**
         * @var Conference $conf
         * @var Conference $nextConf
         */
        $conf = $confRepo->findOneBy(['year' => date("Y")]);
        $nextConf = $confRepo->findOneBy(['year' => date("Y") + 1]);

        $reg_start = $conf->getRegistrationStart()->getTimestamp();
        $reg_finish = $conf->getRegistrationFinish()->getTimestamp();
        $event_start = $conf->getStart()->getTimestamp();
        $event_finish = $conf->getFinish()->getTimestamp();
        $reg_start_next_year = isset($nextConf) ? $nextConf->getRegistrationStart()->getTimestamp() : false;
        $now = time();

        switch (true) {
            /**
             * Pre registration time
             */
            case ($now < $reg_start):
                $countdown_date = $reg_start;
                $text = "До начала регистрации";
                break;
            /**
             * Registration time
             */
            case ($reg_start < $now && $now < $reg_finish):
                $countdown_date = $reg_finish;
                $text = "До конца регистрации";
                break;

            /**
             * Pre event time
             */
            case ($reg_finish < $now && $now < $event_start):
                $countdown_date = $event_start;
                $text = "До начала мероприятия";
                break;

            /**
             * Pre registration time for next year
             */
            case ($reg_start_next_year && $event_finish < $now && $now < $reg_start_next_year):
                $countdown_date = $reg_start_next_year;
                $text = "До начала регистрации";
                break;

            /**
             * Default
             */
            default:
                $countdown_date = false;
                $text = false;
                break;
        }

        return $this->render('default/countdown.html.twig', array(
            'base_dir'          => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'countdown_date'    => $countdown_date,
            'text'              => $text,
            'mainPage'          => $mainPage
        ));
    }
}
