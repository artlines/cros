<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Participating\Speaker;
use App\Entity\Program\ProgramMember;
use App\Repository\Program\ProgramMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SpeakersController extends AbstractController
{
    /**
     * @Route("/speakers", name="speakers")
     * @Route("/speakers/2018")
     *
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function speakers(EntityManagerInterface $em)
    {
        return $this->redirectToRoute('info', ['alias' => 'become-speaker']);

        /** @var Conference $conference */
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var ProgramMemberRepository $programMembersRepo */
        $programMembersRepo = $em->getRepository(ProgramMember::class);

        $speakers = $programMembersRepo->findByData([
            'type' => ProgramMember::TYPE_SPEAKER,
            'year' => $conference->getYear(),
        ]);

        $speakerList = [];
        foreach ($speakers as $key => $speaker) {
            $speakerList[$key]['id']                = $speaker->getId();
            $speakerList[$key]['AvatarSmall']       = $speaker->getAvatarSmall();
            $speakerList[$key]['Organization']      = $speaker->getOrganization();
            $speakerList[$key]['SpeakerFirstName']  = $speaker->getFirstName();
            $speakerList[$key]['SpeakerLastName']   = $speaker->getLastName();
            $speakerList[$key]['SpeakerMiddleName'] = $speaker->getMiddleName();
        }

        shuffle($speakerList);

        return $this->render('frontend/speakers/list.html.twig', [
            'list' => $speakerList,
        ]);
    }

    /**
     * @Route("/speaker/{id}", name="speaker")
     * @param integer $id
     * @return object
     */
    public function speaker($id)
    {
        /** @var Speaker $speaker */
        $speaker = $this->getDoctrine()
            ->getRepository('App\Entity\Participating\Speaker')
            ->find($id);

        return $this->render('frontend/speakers/show.html.twig', [
            'orgname'       => $speaker->getOrganization(),
            'firstname'     => $speaker->getFirstName(),
            'lastname'      => $speaker->getLastName(),
            'description'   => $speaker->getDescription(),
            'avatar'        => $speaker->getAvatarBig()
        ]);
    }
}
