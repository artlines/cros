<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Housing;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class HousingController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__housing_")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class HousingController extends ApiController
{
    /**
     * @Route("housing", methods={"GET"}, name="all")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll()
    {
        /** @var Housing[] $items */
        $items = $this->em->getRepository(Housing::class)->findAll();

        $list = [];
        foreach ($items as $item) {
            $list[] = $this->getResponseItem($item);
        }

        return $this->success($list);
    }

    /**
     * @Route("housing/{id}", requirements={"id": "\d+"}, methods={"GET"}, name="one")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne($id)
    {
        /** @var Housing $housing */
        $housing = $this->findEntity($id);

        $item = $this->getResponseItem($housing);

        return $this->success($item);
    }

    /**
     * @Route("housing/new", methods={"POST"}, name="create")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function create()
    {
        $housing = new Housing();
        $housing->setTitle($this->requestData['title']);
        $housing->setDescription($this->requestData['description']);
        $housing->setNumOfFloors($this->requestData['num_of_floors']);

        try {
            $this->em->persist($housing);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        return $this->created($housing->getId());
    }

    /**
     * @Route("housing/{id}", requirements={"id": "\d+"}, methods={"PUT"}, name="update")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update($id)
    {
        /** @var Housing $housing */
        $housing = $this->findEntity($id);

        $housing->setTitle($this->requestData['title']);
        $housing->setDescription($this->requestData['description']);
        $housing->setNumOfFloors($this->requestData['num_of_floors']);

        try {
            $this->em->persist($housing);
            $this->em->flush();
        } catch (\Exception $e) {
            return $this->exception($e);
        }

        return $this->success();
    }

    /**
     * @Route("housing/{id}", requirements={"id":"\d+"}, methods={"DELETE"}, name="delete")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function delete($id)
    {
        $item = $this->findEntity($id);

        $this->em->remove($item);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return null|object|\Symfony\Component\HttpFoundation\JsonResponse
     */
    private function findEntity($id)
    {
        if (!$item = $this->em->getRepository(Housing::class)->find($id)) {
            return $this->notFound();
        }

        return $item;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Housing $housing
     * @return array
     */
    private function getResponseItem(Housing $housing)
    {
        $_abode_info = [];

        $item = [
            'id'            => $housing->getId(),
            'num_of_floors' => $housing->getNumOfFloors(),
            'title'         => $housing->getTitle(),
            'description'   => $housing->getDescription(),
            'abode_info'    => $_abode_info,
        ];

        return $item;
    }
}