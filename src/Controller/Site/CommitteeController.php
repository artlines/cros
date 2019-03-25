<?php

namespace App\Controller\Site;

use App\Entity\Conference;
use App\Entity\Program\ProgramMember;
use App\Repository\Program\ProgramMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommitteeController extends AbstractController
{
    /**
     * @Route("/committee", name="committee_list")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return
     */
    public function list(Request $request, EntityManagerInterface $em)
    {
        /** @var Conference $conference */
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var ProgramMemberRepository $programMembersRepo */
        $programMembersRepo = $em->getRepository(ProgramMember::class);

        $speakers = $programMembersRepo->findByData([
            'type'      => ProgramMember::TYPE_COMMITTEE,
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

        return $this->render('site/committee/list.html.twig', [
            'list' => $speakerList,
        ]);
    }

    /**
     * @Route("/committee/{id}", name="committee_card")
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return object
     */
    public function card($id, Request $request, EntityManagerInterface $em)
    {
        /** @var Conference $conference */
        $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

        /** @var ProgramMemberRepository $programMembersRepo */
        $programMembersRepo = $em->getRepository(ProgramMember::class);

        $committeeMembers = $programMembersRepo->findByData([
            'id'        => $id,
            'type'      => ProgramMember::TYPE_COMMITTEE,
            'year'      => $conference->getYear(),
            'publish'   => true,
        ]);

        if (!isset($committeeMembers[0])) {
            throw $this->createNotFoundException('Спикер не найден.');
        }

        $committeeMember = $committeeMembers[0];

        return $this->render('site/committee/show.html.twig', [
            'orgname'       => $committeeMember['org_name'],
            'firstname'     => $committeeMember['first_name'],
            'lastname'      => $committeeMember['last_name'],
            'description'   => $committeeMember['description'],
            'avatar'        => $committeeMember['photo_original']
        ]);
    }
}