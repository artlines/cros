<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Participating\ConferenceOrganization;
use App\Repository\ConferenceRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ConferenceMembersController extends AbstractController
{
    /**
     * @Route("/members/{year}", name="members")
     * @param null $year
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function members($year = null)
    {

        $year = date("Y");

        /** @var Conference $conference */
        $conference = $this
            ->getDoctrine()
            ->getRepository(Conference::class)
            ->findOneBy(['year' => $year]);

        if (!$conference) {
            throw $this->createNotFoundException('Страница конференции не найдена');
        }

        /** @var ConferenceOrganization $сonferenceOrganizations */
        $conferenceOrganizations = $this
            ->getDoctrine()
            ->getRepository(ConferenceOrganization::class)
            ->findBy([
                'conference' => $conference,
            ]);

        return $this->render('frontend/members/list.html.twig', [
            'conference'   => $conference,
            'conferenceOrganizations' => $conferenceOrganizations

//            'year'  => $year,
//            'info'          => $info,
        ]);

        /** @var User[] $users */
        list($users, $totalCount) = $userRepo->searchBy($this->requestData);

        return $this->success(['items' => $users, 'total_count' => $totalCount]);


        /** @var ConferenceRepository $conferences */
        $confRepo = $this->getDoctrine()
            ->getRepository(Conference::class);

        /** @var Conference[] $conferences */
        $conferences = $confRepo->findBy([]);

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
            ->getRepository('App\Entity\Content\Info')
            ->findOneBy(['alias' => 'result', 'conference' => $conference]);

        return $this->render('frontend/archive/list.html.twig', [
            'conferences'   => $conferences,
            'selectedyear'  => $year,
            'info'          => $info,
        ]);
    }
}
