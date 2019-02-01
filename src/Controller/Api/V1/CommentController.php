<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\Comment;
use App\Entity\Participating\ConferenceOrganization;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class HousingController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__comment__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class CommentController extends ApiController
{
    /**
     * @Route("comment", methods={"GET"}, name="get_all")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function getAll()
    {
        $orgId = $this->requestData['conference_organization_id'] ?? null;
        if (!$orgId) {
            return $this->badRequest('Missing organization_id.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        $conferenceOrganization = $this->em->find(ConferenceOrganization::class, $orgId);
        if (!$conferenceOrganization) {
            return $this->notFound('Conference organization not found.');
        }

        $items = [];
        /** @var Comment $comment */
        foreach ($conferenceOrganization->getComments() as $comment) {
            $user = $comment->getUser();

            $items[] = [
                'id'            => $comment->getId(),
                'content'       => $comment->getContent(),
                'created_at'    => $comment->getCreatedAt()->getTimestamp(),
                'is_private'    => $comment->isPrivate(),
                'user'  => [
                    'id'    => $user->getId(),
                    'name'  => $user->getFullName(),
                    'email' => $user->getEmail(),
                ],
            ];
        }

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("comment/new", methods={"POST"}, name="new")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function new()
    {
        $orgId = isset($this->requestData['organization_id']) ? (int) $this->requestData['organization_id'] : null;
        if (!$orgId) {
            return $this->badRequest('Missing organization_id.');
        }

        /** @var ConferenceOrganization $conferenceOrganization */
        if (!$conferenceOrganization = $this->em->find(ConferenceOrganization::class, $orgId)) {
            return $this->notFound('Organization not found.');
        }

        $content = isset($this->requestData['content']) ? (string) $this->requestData['content'] : null;
        if (!$orgId) {
            return $this->badRequest('Missing content.');
        }

        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setConferenceOrganization($conferenceOrganization);
        $comment->setContent($content);

        $isPrivate = isset($this->requestData['is_private']) ? (bool) $this->requestData['is_private'] : null;
        if (!is_null($isPrivate)) {
            $comment->setIsPrivate($isPrivate);
        }

        $this->em->persist($comment);
        $this->em->flush();

        return $this->success();
    }
}