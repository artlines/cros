<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Speaker;
use App\Repository\ConferenceRepository;
use App\Repository\SpeakerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $year = date("Y");
        $em = $this->getDoctrine()->getManager();

        /** @var ConferenceRepository $conferenceRepository */
        $conferenceRepository = $em->getRepository('App:Conference');
        /** @var Conference $conf */
        $conf = $conferenceRepository->findOneBy(['year' => $year]);

        /** @var SpeakerRepository $speakerRepository */
        $speakerRepository = $em->getRepository('App:Speaker');
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

        return $this->render('site/main_page.html.twig', [
            'speaker_list'  => $speakerList,
        ]);
    }
}