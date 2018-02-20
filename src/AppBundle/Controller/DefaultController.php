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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/new", name="cros2-main")
     */
    public function newMainAction()
    {
        return $this->render('cros2/base.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/", name="homepage")
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
     * @Route("/forum", name="forum")
     /
    public function forumAction()
    {
        /** @var Setting $settings /
    }
     */

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
}
