<?php

namespace App\Controller\Api\V1\Program;

use App\Controller\Api\V1\ApiController;
use App\Entity\Participating\ConferenceMember;
use App\Entity\Program\ProgramMember;
use App\Repository\Program\ProgramMemberRepository;
use App\Service\FileManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ApartmentController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__program_member__")
 * @IsGranted("ROLE_CONTENT_MANAGER")
 */
class ProgramMemberController extends ApiController
{
    /**
     * @Route("program_member", name="program_member__get", methods={"GET"})
     */
    public function getAll()
    {
        /** @var ProgramMemberRepository $programMemberRepo */
        $programMemberRepo = $this->em->getRepository(ProgramMember::class);

        $items = $programMemberRepo->findByData($this->requestData);

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("program_member/new", name="program_member__new", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param FileManager $fileManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function new(FileManager $fileManager)
    {
        $photoOriginal = $this->requestData['photo_original'] ?? null;
        $conferenceMemberId = $this->requestData['conference_member_id'] ?? null;
        $description = $this->requestData['description'] ?? null;
        $type = $this->requestData['type'] ?? null;
        $publish = $this->requestData['publish'] ?? true;
        $ordering = $this->requestData['ordering'] ?? 100;

        if (!$conferenceMemberId) {
            return $this->badRequest('Не указан участник.');
        }

        if (!$description) {
            return $this->badRequest('Не указано описание.');
        }

        if (!$type) {
            return $this->badRequest('Ошибка при указании типа участника. Обратитесь в тех. поддержку.');
        }

        /** @var ConferenceMember $conferenceMember */
        if (!$conferenceMember = $this->em->find(ConferenceMember::class, (int) $conferenceMemberId)) {
            return $this->notFound('Conference member with ID '.$conferenceMemberId.' not found.');
        }

        /** Check that member has been already added as {$type} */
        if ($this->em->getRepository(ProgramMember::class)->findOneBy(['type' => $type, 'conferenceMember' => $conferenceMember])) {
            return $this->badRequest('Пользователь уже добавлен.');
        }

        $programMember = new ProgramMember();
        $programMember->setConferenceMember($conferenceMember);
        $programMember->setDescription($description);
        $programMember->setType($type);
        $programMember->setPublish($publish);
        $programMember->setOrdering($ordering);

        if (!is_null($photoOriginal)) {
            $newPhotoOriginal = $fileManager->uploadBase64($photoOriginal, 'speaker');
            $programMember->setPhotoOriginal($newPhotoOriginal);
        }

        $this->em->persist($programMember);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("program_member/{id}", requirements={"id":"\d+"}, name="program_member__edit", methods={"PUT"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @param FileManager $fileManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id, FileManager $fileManager)
    {
        $photoOriginal = $this->requestData['photo_original'] ?? null;
        $description = $this->requestData['description'] ?? null;
        $type = $this->requestData['type'] ?? null;
        $publish = $this->requestData['publish'] ?? null;
        $ordering = $this->requestData['ordering'] ?? null;

        /** @var ProgramMember $programMember */
        if (!$programMember = $this->em->find(ProgramMember::class, $id)) {
            return $this->notFound('Program member with ID '.$id.' not found.');
        }

        if (!is_null($description)) {
            $programMember->setDescription($description);
        }

        if (!is_null($type)) {
            $programMember->setType($type);
        }

        if (!is_null($publish)) {
            $programMember->setPublish($publish);
        }

        if (!is_null($ordering)) {
            $programMember->setOrdering($ordering);
        }

        if (!is_null($photoOriginal) && $photoOriginal !== $programMember->getPhotoOriginal()) {
            $newPhotoOriginal = $fileManager->uploadBase64($photoOriginal, 'speaker');
            $programMember->setPhotoOriginal($newPhotoOriginal);
        }

        $this->em->persist($programMember);
        $this->em->flush();

        return $this->success();
    }
}