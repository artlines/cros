<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\Lecture;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\Program;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProgramController extends Controller
{

    const DEFAULT_HALL = 'Большой зал';

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/program", name="program")
     */
    public function programAction()
    {
        /** @var Conference $conf */
        $lectures = $this->getDoctrine()
            ->getRepository('AppBundle:Lecture')
            ->findBy([], ['date' => 'ASC', 'startTime' => 'ASC', 'endTime' => 'ASC']);

        $all_halls = [self::DEFAULT_HALL];
        $program = [];
        /** @var Lecture $lecture */
        foreach ($lectures as $lecture)
        {
            $_day_key = $lecture->getDate()->format('d.m.Y');
            $_time_key = $lecture->getStartTime()->format("H:i")." - ".$lecture->getEndTime()->format("H:i");
            $_hall_key = $lecture->getHall();

            if (!$lecture->getSpeaker()) {
                $program[$_day_key][$_time_key][self::DEFAULT_HALL] = $lecture;
            } else {
                $program[$_day_key][$_time_key][$_hall_key] = $lecture;
                if (!in_array($_hall_key, $all_halls)) $all_halls[] = $_hall_key;
            };
        }

        return $this->render('frontend/program/show_new.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'program' => $program,
            'lectures' => $lectures,
            'all_halls' => $all_halls,
            'cars' => []
        ));
    }

    /**
     * @Route("/old-program", name="old-program")
     */
    public function oldProgramAction()
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
        $month = $monthes[$event_date];

        /** @var Program $programs */
        $programs = $this->getDoctrine()
            ->getRepository('AppBundle:Program')
            ->findBy(array('conferenceId' => $conf->getId()), array('date' => 'ASC', 'start' => 'ASC'));

        $days = array();

        foreach ($programs as $program){
            $days[$program->getDate()] = $program->getDate();
        }

        /** @var AppendText $append_text */
        $append_text = $this->getDoctrine()
            ->getRepository('AppBundle:AppendText')
            ->findOneBy(array('alias' => 'program'));

        return $this->render('frontend/program/show.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'programs' => $programs,
            'append_text' => $append_text,
            'days' => $days,
            'month' => $month,
        ));
    }
}
