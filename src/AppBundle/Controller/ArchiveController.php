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

class ArchiveController extends Controller
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/archive/{year}", name="archive")
     */
    public function archiveAction($year = null)
    {
        /** @var Conference $conferences */
        $conferences = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findWithArchiveOnly();

        $founded = false;
        $pre_year = $year;
        $last_active = null;

        /** @var Conference $conference */
        foreach ($conferences as $conference){
            if($conference->getYear() == $pre_year){
                $year = $conference->getYear();
                $founded = true;
                break;
            }
            if($conference->getYear() > $year){
                $year = $conference->getYear();
                $founded = true;
            }
            elseif($last_active < $conference->getYear()){
                $last_active = $conference->getYear();
            }
        }
        if(!$founded){
            return $this->redirectToRoute('archive', array('year' => $last_active));
        }
        return $this->render('frontend/archive/list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'conferences' => $conferences,
            'selectedyear' => $year,
        ));
    }
}
