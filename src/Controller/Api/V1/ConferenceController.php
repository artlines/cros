<?php

namespace App\Controller\Api\V1;

use App\Entity\Conference;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ConferenceController
 * @package App\Controller\Api\V1
 *
 * @IsGranted("ROLE_ADMINISTRATOR")
 * @Route("/api/v1/", name="api_v1__conference_")
 */
class ConferenceController extends ApiController
{
    /**
     * @Route("conference", methods={"GET"})
     */
    public function getAll()
    {
        /** @var Conference[] $conferences */
        $conferences = $this->em->getRepository(Conference::class)->findAll();

        $items = [];
        foreach ($conferences as $conference) {
            $items[] = [
                'id'                => $conference->getId(),
                'year'              => $conference->getYear(),
                'reg_start'         => $conference->getRegistrationStart()->format('Y-m-d\TH:i'),
                'reg_finish'        => $conference->getRegistrationFinish()->format('Y-m-d\TH:i'),
                'event_start'       => $conference->getEventStart()->format('Y-m-d\TH:i'),
                'event_finish'      => $conference->getEventFinish()->format('Y-m-d\TH:i'),
                'users_limit_global' => $conference->getLimitUsersGlobal(),
                'users_limit_by_org' => $conference->getLimitUsersByOrg(),
            ];
        }

        return $this->success(['items' => $items]);
    }

    /**
     * @Route("conference/new", methods={"POST"})
     */
    public function new()
    {
        $year = $this->requestData['year'] ?? null;
        $reg_start = $this->requestData['reg_start'] ?? null;
        $reg_finish = $this->requestData['reg_finish'] ?? null;
        $event_start = $this->requestData['event_start'] ?? null;
        $event_finish = $this->requestData['event_finish'] ?? null;
        $users_limit_global = $this->requestData['users_limit_global'] ?? null;
        $users_limit_by_org = $this->requestData['users_limit_by_org'] ?? null;

        if (!$year || !$reg_start || !$reg_finish || !$event_start || !$event_finish || !$users_limit_global || !$users_limit_by_org) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        if ($this->em->getRepository(Conference::class)->findOneBy(['year' => $year])) {
            return $this->badRequest('Конференция с указанным годом уже существует.');
        }

        $conference = new Conference();

        $conference->setYear($year);
        $conference->setRegistrationStart(new \DateTime($reg_start));
        $conference->setRegistrationFinish(new \DateTime($reg_finish));
        $conference->setEventStart(new \DateTime($event_start));
        $conference->setEventFinish(new \DateTime($event_finish));
        $conference->setLimitUsersGlobal($users_limit_global);
        $conference->setLimitUsersByOrg($users_limit_by_org);

        $this->em->persist($conference);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference/{id}", requirements={"id":"\d+"}, methods={"PUT"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function edit($id)
    {
        $year = $this->requestData['year'] ?? null;
        $reg_start = $this->requestData['reg_start'] ?? null;
        $reg_finish = $this->requestData['reg_finish'] ?? null;
        $event_start = $this->requestData['event_start'] ?? null;
        $event_finish = $this->requestData['event_finish'] ?? null;
        $users_limit_global = $this->requestData['users_limit_global'] ?? null;
        $users_limit_by_org = $this->requestData['users_limit_by_org'] ?? null;

        if (!$year || !$reg_start || !$reg_finish || !$event_start || !$event_finish || !$users_limit_global || !$users_limit_by_org) {
            return $this->badRequest('Не указаны обязательные параметры.');
        }

        /** @var Conference $conference */
        if (!$conference = $this->em->find(Conference::class, $id)) {
            return $this->notFound('Conference not found.');
        }

        if ($year !== $conference->getYear() && $this->em->getRepository(Conference::class)->findOneBy(['year' => $year])) {
            return $this->badRequest('Конференция с указанным годом уже существует.');
        }

        $conference->setYear($year);
        $conference->setRegistrationStart(new \DateTime($reg_start));
        $conference->setRegistrationFinish(new \DateTime($reg_finish));
        $conference->setEventStart(new \DateTime($event_start));
        $conference->setEventFinish(new \DateTime($event_finish));
        $conference->setLimitUsersGlobal($users_limit_global);
        $conference->setLimitUsersByOrg($users_limit_by_org);

        $this->em->persist($conference);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @Route("conference/{id}", requirements={"id":"\d+"}, methods={"DELETE"})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        /** @var Conference $conference */
        if (!$conference = $this->em->find(Conference::class, $id)) {
            return $this->notFound('Conference not found.');
        }

        try {
            $this->em->remove($conference);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->badRequest('С данной конференцией слишком много связано. Нельзя ее удалять.');
        }

        return $this->success();
    }
}