<?php

namespace App\Controller\Api\V1;

use App\Entity\Abode\Housing;
use App\Manager\AbodeManager;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class HousingController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__housing__")
 * @IsGranted("ROLE_SETTLEMENT_MANAGER")
 */
class HousingController extends ApiController
{
    /** @var AbodeManager|null */
    protected $am;

    /**
     * @Route("housing/{id}/resettlement", requirements={"id":"\d+"}, methods={"GET"}, name="resettlement")
     * @param $id
     * @param AbodeManager $am
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getResettlement($id, AbodeManager $am)
    {
        /** @var Housing $housing */
        if (!$housing = $this->em->find(Housing::class, $id)) {
            return $this->notFound('Housing not found.');
        }

        $result = $am->calculateResettlementByHousing($housing);

        return $this->success(['items' => $result]);
    }

    /**
     * @Route("housing", methods={"GET"}, name="get_all")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param AbodeManager $abodeManager
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAll(AbodeManager $abodeManager)
    {
        $this->am = $abodeManager;

        /** @var Housing[] $housings */
        $housings = $this->em->getRepository(Housing::class)->findAll();

        $items = [];
        foreach ($housings as $housing) {
            $items[] = $this->getResponseItem($housing);
        }

        return $this->success(['items' => $items, 'total_count' => count($items)]);
    }

    /**
     * @Route("housing/{id}", requirements={"id": "\d+"}, methods={"GET"}, name="get_one")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @param AbodeManager $am
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOne($id, AbodeManager $am)
    {
        $this->am = $am;

        try {
            /** @var Housing $housing */
            $housing = $this->findEntity($id);
        } catch (EntityNotFoundException $e) {
            return $this->notFound();
        }

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
        try {
            /** @var Housing $housing */
            $housing = $this->findEntity($id);
        } catch (EntityNotFoundException $e) {
            return $this->notFound();
        }

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
        try {
            $item = $this->findEntity($id);
        } catch (EntityNotFoundException $e) {
            return $this->notFound();
        }

        $this->em->remove($item);
        $this->em->flush();

        return $this->success();
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $id
     * @return null|object|\Symfony\Component\HttpFoundation\JsonResponse
     * @throws EntityNotFoundException
     */
    private function findEntity($id)
    {
        if (!$item = $this->em->getRepository(Housing::class)->find($id)) {
            throw new EntityNotFoundException();
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

        $item['abode_info'] = $this->am->calculateAbodeInfoByHousing($housing);

        return $item;
    }
}