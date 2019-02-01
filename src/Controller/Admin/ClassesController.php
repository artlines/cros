<?php

namespace App\Controller\Admin;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\ApartamentPair;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Corpuses;
use AppBundle\Entity\Flat;
use AppBundle\Entity\Stage;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use AppBundle\Repository\ApartamentPairRepository;
use AppBundle\Repository\CorpusesRepository;
use AppBundle\Repository\FlatRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClassesController extends AbstractController
{
    /**
     * Список зарегистрированных пользователей
     *
     * @Route("/admin/classes/{year}/{signed}", name="admin-classes")
     * @Route("/admin/classes/{year}")
     *
     * @param integer $year
     * @param string $signed
     *
     * @return object
     */
    public function adminClasses($year = null, $signed = 'all')
    {
        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        /** @var ApartamentPair $pairs */
        $pairs = $this->getDoctrine()
            ->getRepository('App:ApartamentPair')
            ->findFull($conf->getId());

        /** @var ApartamentId $nifs */
        $nifs = $this->getDoctrine()
            ->getRepository('App:ApartamentId')
            ->findAllWithUserNotInFlat($conf->getId());

        return $this->render('admin/classes/byapart.html.twig', array(
            'signed' => $signed,
            'pairs' => $pairs,
            'year' => $year,
            'nifs' => $nifs,
        ));
    }

    /**
     * Установка номера
     *
     * @Route("/admin/set_number", name="admin-set-number")
     *
     * @param Request $request
     * @return object
     */
    public function setNumber(Request $request){
        $id = $request->get('id');
        $real_id = $request->get('real_id');
        if(!is_numeric($real_id)){
            $real_id = null;
        }

        $em = $this->getDoctrine()->getManager();

        /** @var FlatRepository $flatRepository */
        $flatRepository = $this->getDoctrine()->getRepository('App:Flat');
        /** @var Flat $flat */
        $flat = $flatRepository->find($id);
        $flat->setRealId($real_id);

        $em->persist($flat);
        $em->flush();

        return new Response($id.' '.$real_id);
    }

    /**
     * Сохранение комнат по номерам
     *
     * @Route("/admin/classes-save", name="save-apart-to-classes")
     *
     * @param Request $request
     * @return object
     */
    public function saveClasses(Request $request)
    {
        $save_json = $request->get('save');
        $save = json_decode($save_json, 1);
        $em = $this->getDoctrine()->getManager();

        foreach ($save as $flat_id => $rooms){
            /** @var Flat $flat */
            $flat = $this->getDoctrine()
                ->getRepository('App:Flat')
                ->find($flat_id);

            foreach ($rooms as $room_num => $room_id){
                switch ($room_num){
                    case "room1":
                        $flat->setRoom1($room_id);
                        break;
                    case "room2":
                        $flat->setRoom2($room_id);
                        break;
                    case "room3":
                        $flat->setRoom3($room_id);
                        break;
                    case "room4":
                        $flat->setRoom4($room_id);
                        break;
                    case "room5":
                        $flat->setRoom5($room_id);
                        break;
                }
            }
            if($flat->getRoom1() == ""){
                $flat->setRoom1(null);
            }
            if($flat->getRoom2() == ""){
                $flat->setRoom2(null);
            }
            if($flat->getRoom3() == ""){
                $flat->setRoom3(null);
            }
            if($flat->getRoom4() == ""){
                $flat->setRoom4(null);
            }
            if($flat->getRoom5() == ""){
                $flat->setRoom5(null);
            }
            $em->persist($flat);
            $em->flush();
        }

        $response = new JsonResponse('ok');
        return $response;
    }

    /**
     * Сохранение комнат по номерам
     *
     * @Route("/admin/corpuses-save", name="save-flat-to-corpuses")
     *
     * @param Request $request
     * @return object
     */
    public function saveCorpuses(Request $request)
    {
        $save_json = $request->get('save');
        $save = json_decode($save_json, 1);
        $em = $this->getDoctrine()->getManager();

        foreach ($save as $stage_id => $flats){
            /** @var Stage $stage */
            $stage = $this->getDoctrine()
                ->getRepository('App:Stage')
                ->find($stage_id);

            foreach ($flats as $flat_num => $flat_id){
                switch ($flat_num){
                    case "flat1":
                        $stage->setFlat1($flat_id);
                        break;
                    case "flat2":
                        $stage->setFlat2($flat_id);
                        break;
                    case "flat3":
                        $stage->setFlat3($flat_id);
                        break;
                    case "flat4":
                        $stage->setFlat4($flat_id);
                        break;
                }
            }
            if($stage->getFlat1() == ""){
                $stage->setFlat1(null);
            }
            if($stage->getFlat2() == ""){
                $stage->setFlat2(null);
            }
            if($stage->getFlat3() == ""){
                $stage->setFlat3(null);
            }
            if($stage->getFlat4() == ""){
                $stage->setFlat4(null);
            }
            $em->persist($stage);
            $em->flush();
        }

        $response = new JsonResponse('ok');
        return $response;
    }

    /**
     * Закрываем размещение
     *
     * @Route("/admin/class-block", name="admin-class-block")
     */
    public function classBlock(Request $request){
        $flat_id = $request->get('flat');
        $finished = $request->get('finished');

        $em = $this->getDoctrine()->getManager();

        /** @var Flat $flat */
        $flat = $this->getDoctrine()
            ->getRepository('App:Flat')
            ->find($flat_id);

        if($flat){
            $flat->setFinished($finished);

            $em->persist($flat);
            $em->flush();

            $response = new JsonResponse(json_encode(array('success' => 'true', 'result' => array('flat_id' => $flat_id, 'finished' => $flat->getFinished()))));
        }
        else{
            $response = new JsonResponse(json_encode(array('success' => 'false', 'result' => 'Не удалось найти квартиру с идентификатором "'.$flat_id.'" в базе данных')));
        }

        return $response;
    }

    /**
     * Расселение по корпусам
     * @Route("/admin/corpuses/{year}", name="admin-corpuses")
     *
     * @param integer $year
     * @return object
     */
    public function adminCorpuses($year){
        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        /** @var CorpusesRepository $corpusesRepository */
        $corpusesRepository = $this->getDoctrine()->getRepository('App:Corpuses');
        /** @var Corpuses $corpuses */
        $corpuses = $corpusesRepository->findAll();

        /** @var ApartamentPairRepository $apartamentPairRepository */
        $apartamentPairRepository = $this->getDoctrine()->getRepository('App:ApartamentPair');
        /** @var ApartamentPair $apartamentPair */
        $apartamentPair = $apartamentPairRepository->findFullWoCorpus($conf->getId());

        return $this->render('admin/classes/bycorpuses.html.twig', array(
            'year' => $year,
            'corpuses' => $corpuses,
            'wocs' => $apartamentPair,
        ));
    }
}
