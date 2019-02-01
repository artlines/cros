<?php

namespace App\Controller\Api\V1;

use App\Entity\Participating\ParticipationClass;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ParticipantClassController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class ParticipationClassController extends ApiController
{
    /**
     * @Route("participation_class", methods={"GET"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var ParticipationClass[] $participationClasses */
        $participationClasses = $this->em->getRepository(ParticipationClass::class)->findBy([], ['id' => 'ASC']);

        $items = [];
        foreach ($participationClasses as $class) {
            $items[] = [
                'id'    => $class->getId(),
                'title' => $class->getTitle(),
            ];
        }

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("participation_class/new", methods={"POST"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function new()
    {
        $title = $this->requestData['title'] ?? null;

        if (!$title) {
            return $this->badRequest('Не указано наименование.');
        }

        $class = new ParticipationClass();
        $class->setTitle($title);

        $this->em->persist($class);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("participation_class/{id}", requirements={"id":"\d+"}, methods={"PUT"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $title = $this->requestData['title'] ?? null;

        if (!$title) {
            return $this->badRequest('Не указано наименование.');
        }

        /** @var ParticipationClass $class */
        if (!$class = $this->em->find(ParticipationClass::class, $id)) {
            return $this->notFound('Participation class not found.');
        }

        $class->setTitle($title);

        $this->em->persist($class);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("participation_class/{id}", requirements={"id":"\d+"}, methods={"DELETE"})
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var ParticipationClass $class */
        if (!$class = $this->em->find(ParticipationClass::class, $id)) {
            return $this->notFound('Participation class not found.');
        }

        if ($class->getRoomTypes()->count()) {
            return $this->badRequest('У класса участия есть привязанные типы комнат.');
        }

        $this->em->remove($class);
        $this->em->flush();

        return $this->success();
    }
}