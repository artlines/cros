<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Repository\ConferenceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArchiveController extends AbstractController
{
    /**
     * @Route("/archive/{year}", name="archive")
     * @param null $year
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function archive($year = null)
    {
        /** @var ConferenceRepository $conferences */
        $confRepo = $this->getDoctrine()
            ->getRepository('App\Entity\Conference', 'pgsql');

        /** @var Conference[] $conferences */
        $conferences = $confRepo->findBy([], ['year' => 'desc']);

        $pre_year = $year;
        $last_active = null;
        $founded = false;

        /** @var Conference $conference */
        foreach ($conferences as $conference) {
            if ($conference->getYear() == $pre_year) {
                $year = $conference->getYear();
                $founded = true;
                break;
            }

            if ($conference->getYear() > $year) {
                $year = $conference->getYear();
                $founded = true;
            } elseif ($last_active < $conference->getYear()) {
                $last_active = $conference->getYear();
            }
        }

        if (!$founded) {
            return $this->redirectToRoute('archive', ['year' => $last_active]);
        }

        $info = $this->getDoctrine()
            ->getRepository('App\Entity\Content\Info', 'pgsql')
            ->findOneBy(['alias' => 'result', 'conference' => $conference]);

        return $this->render('frontend/archive/list.html.twig', [
            'conferences'   => $conferences,
            'selectedyear'  => $year,
            'info'          => $info,
        ]);
    }
}
