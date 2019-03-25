<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Content\Faq;
use App\Entity\Participating\Speaker;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(EntityManagerInterface $em)
    {
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var Speaker[] $speakers */
        $speakers = $em->getRepository('App:Participating\Speaker')
            ->findBy(['conference' => $conference, 'publish' => true]);

        $speakers_rand = [];
        if (count($speakers) > 1) {
            $rand_keys = array_rand($speakers, 4);
            foreach ($rand_keys as $val) {
                $speakers_rand[] = $speakers[$val];
            }
        }

        $speakerList = [];
        /** @var Speaker $speaker */
        foreach ($speakers_rand as $key => $speaker) {
            $speakerList[$key]['id']                = $speaker->getId();
            $speakerList[$key]['AvatarSmall']       = $speaker->getAvatarSmall();
            $speakerList[$key]['Organization']      = $speaker->getOrganization();
            $speakerList[$key]['SpeakerFirstName']  = $speaker->getFirstName();
            $speakerList[$key]['SpeakerLastName']   = $speaker->getLastName();
            $speakerList[$key]['SpeakerMiddleName'] = $speaker->getMiddleName();
        }

        return $this->render('site/main_page.html.twig', [
            'speaker_list'  => $speakerList,
        ]);
    }

    /**
     * Generates countdown widget
     * Used as include in other templates
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param bool $mainPage
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countdown($mainPage = false)
    {
        /** @var ConferenceRepository $confRepo */
        $confRepo = $this->getDoctrine()->getRepository('App:Conference');

        /**
         * @var Conference $conf
         * @var Conference $nextConf
         */
        $conf = $confRepo->findOneBy(['year' => date("Y")]);
        $nextConf = $confRepo->findOneBy(['year' => date("Y") + 1]);

        $reg_start = $conf->getRegistrationStart()->getTimestamp();
        $reg_finish = $conf->getRegistrationFinish()->getTimestamp();
        $event_start = $conf->getEventStart()->getTimestamp();
        $event_finish = $conf->getEventFinish()->getTimestamp();
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

        return $this->render('site/common/_countdown.html.twig', [
            'countdown_date'    => $countdown_date,
            'text'              => $text,
            'mainPage'          => $mainPage
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        /** @var Faq $faq */
        $faq = $this->getDoctrine()->getRepository('App\Entity\Content\Faq')
            ->findBy(['isActive' => true]);

        return $this->render('frontend/faq/show.html.twig', [
            'faq' => $faq,
        ]);
    }

    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacy()
    {
        $content = file_get_contents('https://shop.nag.ru/policies/privacy');

        return $this->render('security/privacy.html.twig', [
            'content' => $content,
        ]);
    }
}