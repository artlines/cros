<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\Speaker;
use AppBundle\Entity\User;
use AppBundle\Repository\SpeakerRepository;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SpeakersController extends Controller
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/speakers/{year}", name="speakers")
     * @Route("/speakers")
     *
     * @param integer|boolean $year
     * @param Request $request
     *
     * @return object
     */
    public function speakersAction($year = false, Request $request)
    {
        if(!$year) {
            $year = date("Y");
        }

        $conferenceRepository = $this->getDoctrine()->getRepository('AppBundle:Conference');

        $conferences = $conferenceRepository->findBy(array(), array('id' => 'DESC'));

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        /** @var Speaker $speakers */
        $speakers = $speakerRepository->findByConf($conf->getId());

        return $this->render('frontend/speakers/list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'list' => $speakers,
            'conferences' => $conferences,
            'selectedyear' => $year,
        ));
    }

    /**
     * @Route("speaker/{id}", name="speaker")
     * @param integer $id
     * @return object
     */
    public function speakerAction($id){
        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $this->getDoctrine()->getRepository('AppBundle:Speaker');
        /** @var Speaker $speakers */
        $speaker = $speakerRepository->find($id);

        return $this->render('frontend/speakers/show.html.twig', array(
            'speaker' => $speaker
        ));
    }
}
