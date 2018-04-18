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


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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
     * @Route("/", name="homepage")
     */
    public function newMainAction()
    {
        $reg_time = $this->getDoctrine()->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date("Y")));

        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        $speaker = $speakerRepository->findAll();
        $speakerList = NULL;
        foreach ($speaker as $key =>  $value){
            $speakerList[$key]['AvatarSmall'] = $value->getAvatarSmall();
            $speakerList[$key]['Organization'] = $value->getUser()->getOrganization()->getName();
            $speakerList[$key]['SpeakerFirstName'] = $value->getUser()->getFirstName();
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
