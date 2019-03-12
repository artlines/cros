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

        /** @var Conference $conference */
        $conference = $this
            ->getDoctrine()
            ->getRepository(Conference::class)
            ->findOneBy(['year' => $year]);

        if (!$conference) {
            throw $this->createNotFoundException('Страница конференции не найдена');
        }
        /** @var ConferenceOrganization $conferenceOrganizations */
        $conferenceOrganizations = $this
            ->getDoctrine()
            ->getRepository(ConferenceOrganization::class)
            ->findShowByConference($conference);

        return $this->render('frontend/members/list.html.twig', [
            'conference'   => $conference,
            'conferenceOrganizations' => $conferenceOrganizations
        ]);
    }
}
