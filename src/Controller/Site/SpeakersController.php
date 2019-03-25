<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Program\ProgramMember;
use App\Repository\Program\ProgramMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SpeakersController extends AbstractController
{
    /**
     * @Route("/speakers", name="speakers")
     *
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function speakers(Request $request, EntityManagerInterface $em)
    {
        if ($request->get('auth', null) !== 'ochoquo7PheilauZ9eoleoyah4xae8os') {
            return $this->redirectToRoute('info', ['alias' => 'become-speaker']);
        }

        /** @var Conference $conference */
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var ProgramMemberRepository $programMembersRepo */
        $programMembersRepo = $em->getRepository(ProgramMember::class);

        $speakers = $programMembersRepo->findByData([
            'type'      => ProgramMember::TYPE_SPEAKER,
            'year'      => $conference->getYear(),
            'publish'   => true,
        ]);

        $speakerList = [];
        foreach ($speakers as $key => $speaker) {
            $speakerList[$key]['id']                = $speaker['id'];
            $speakerList[$key]['AvatarSmall']       = $speaker['photo_original'];
            $speakerList[$key]['Organization']      = $speaker['org_name'];
            $speakerList[$key]['SpeakerFirstName']  = $speaker['first_name'];
            $speakerList[$key]['SpeakerLastName']   = $speaker['last_name'];
            $speakerList[$key]['SpeakerMiddleName'] = $speaker['middle_name'];
        }

        shuffle($speakerList);

        return $this->render('site/speakers/list.html.twig', [
            'list' => $speakerList,
        ]);
    }

    /**
     * @Route("/speaker/{id}", name="speaker")
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return object
     */
    public function speaker($id, Request $request, EntityManagerInterface $em)
    {
        if ($request->get('auth', null) !== 'ochoquo7PheilauZ9eoleoyah4xae8os') {
            return $this->redirectToRoute('info', ['alias' => 'become-speaker']);
        }

        /** @var Conference $conference */
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var ProgramMemberRepository $programMembersRepo */
        $programMembersRepo = $em->getRepository(ProgramMember::class);

        $speakers = $programMembersRepo->findByData([
            'id'        => $id,
            'type'      => ProgramMember::TYPE_SPEAKER,
            'year'      => $conference->getYear(),
            'publish'   => true,
        ]);

        if (!isset($speakers[0])) {
            throw $this->createNotFoundException('Спикер не найден.');
        }

        $speaker = $speakers[0];

        return $this->render('site/speakers/show.html.twig', [
            'orgname'       => $speaker['org_name'],
            'firstname'     => $speaker['first_name'],
            'lastname'      => $speaker['last_name'],
            'description'   => $speaker['description'],
            'avatar'        => $speaker['photo_original']
        ]);
    }
}
