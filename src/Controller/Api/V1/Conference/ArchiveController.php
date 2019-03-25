<?php

namespace App\Controller\Api\V1\Conference;

use App\Controller\Api\V1\ApiController;
use App\Entity\Conference;
use App\Entity\Content\Info;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ArchiveController
 * @package App\Controller\Api\V1\Conference
 *
 * @IsGranted("ROLE_CONTENT_MANAGER")
 * @Route("/api/v1/conference", name="api_v1__conference_")
 */
class ArchiveController extends ApiController
{
    /**
     * @Route("/{id}/archive", name="archive_get", requirements={"id":"\d+"}, methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne($id)
    {
        /** @var Conference $conference */
        $conference = $this->em->find(Conference::class, $id);

        if (!$conference) {
            return $this->notFound('Conference not found.');
        }

        /** @var Info $archive */
        $archive = $this->em->getRepository(Info::class)->findOneBy([
            'conference'    => $conference,
            'alias'         => Info::ALIAS__ARCHIVE,
        ]);

        if (!$archive) {
            return $this->notFound('Archive not found.');
        }

        $content = $archive->getContent();

        return $this->success(['content' => $content]);
    }

    /**
     * @Route("/{id}/archive", name="archive_update", requirements={"id":"\d+"}, methods={"PUT"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id)
    {
        if (!$content = $this->requestData['content'] ?? null) {
            return $this->badRequest('Не передано содержимое архива.');
        }

        /** @var Conference $conference */
        if (!$conference = $this->em->find(Conference::class, $id)) {
            return $this->notFound('Conference not found.');
        }

        /** @var Info $archive */
        $archive = $this->em->getRepository(Info::class)->findOneBy([
            'conference'    => $conference,
            'alias'         => Info::ALIAS__ARCHIVE,
        ]);

        if (!$archive) {
            return $this->notFound('Archive not found.');
        }

        $archive->setContent($content);
        $this->em->persist($archive);
        $this->em->flush();

        return $this->success();
    }
}