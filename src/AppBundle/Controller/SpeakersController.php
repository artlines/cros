<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\SpeakerReports;
use AppBundle\Entity\User;
use AppBundle\Repository\SpeakerRepository;
use AppBundle\Repository\SpeakerReportsRepository;
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
        $speakerList = NULL;
        foreach ($speakers as $key =>  $value){
            $speakerList[$key]['id'] = $value->getid();
            $speakerList[$key]['AvatarSmall'] = $value->getAvatarSmall();
            $speakerList[$key]['Organization'] = $value->getUser()->getOrganization()->getName();
            $speakerList[$key]['SpeakerFirstName'] = $value->getUser()->getFirstName();
            $speakerList[$key]['SpeakerLastName'] = $value->getUser()->getLastName();
            $speakerList[$key]['SpeakerMiddleName'] = $value->getUser()->getMiddleName();
        }
        shuffle ($speakerList);
        return $this->render('frontend/speakers/list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'list' => $speakerList,
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
        $speakerReportsRepository = $this->getDoctrine()->getRepository('AppBundle:SpeakerReports');
        $report = $speakerReportsRepository->findBy(array('speaker_id' => $id));
        $orgname = $speaker->getUser()->getOrganization()->getName();
        $firstName = $speaker->getUser()->getFirstName();
        $LastName = $speaker->getUser()->getLastName();
        $MiddleName = $speaker->getUser()->getMiddleName();
        $Description = $speaker->getDescription();
        $avatar = $speaker->getAvatarBig();

        return $this->render('frontend/speakers/show.html.twig', array(
            'speaker' => $speaker,
            'orgname' => $orgname,
            'firstName' => $firstName,
            'Lastname' => $LastName,
            'MiddleName' => $MiddleName,
            'report_list' => $report,
            'description' => $Description,
            'avatar' => $avatar
        ));
    }
    /**
     * @Route("speaker/all", name="speaker-all")
     * @return object
     */
    public function speakerAll($id){
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
        $speakerList = NULL;
        foreach ($speakers as $key =>  $value){
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
}
