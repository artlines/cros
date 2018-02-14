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

class InfoController extends Controller
{
    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/test/{alias}", name="info_test")
     */
    public function testAction($alias){
		$infoRepository = $this->getDoctrine()->getRepository('AppBundle:Info');
		$info = $infoRepository->findInfoByAlias($alias, '2018');
		//$info1 = $infoRepository->find(118)->getConftoinfos();

		//var_dump($info);
		//var_dump($info1);
    }

    /**
     * @Route("/info/{alias}", name="info")
     */
    public function infoAction($alias){

		if ($alias == 'organize') {
			$_dates = $this->getDoctrine()->getRepository('AppBundle:Conference')
					->findOneBy(array('year' => date("Y")));

			$dates = array(
					$_dates->getStart()->getTimestamp(),
					$_dates->getFinish()->getTimestamp()
				);
			setlocale(LC_ALL, 'ru_RU');
			//$debug = strftime("%e %B %G", $_dates->getFinish()->getTimestamp());
			//var_dump($debug);

			return $this->render('frontend/info/organize.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
				'dates' => $dates
            ));
		};
        
        $info = null;
    	if(in_array($alias, ['place', 'result', 'terms', 'transfer', 'targets'])){
			$infoRepository = $this->getDoctrine()->getRepository('AppBundle:Info');
			$info = $infoRepository->findInfoByAlias($alias, date("Y"));
		}
		else{
	    		/** @var Info $info */
			$info = $this->getDoctrine()
		    		->getRepository('AppBundle:Info')
		    		->findOneBy(array('alias' => $alias));
		}
        
        if($info){
            return $this->render('frontend/info/show.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'info' => $info,
            ));
        }
        else{
            throw $this->createNotFoundException('Страница не найдена');
        }
    }
}
