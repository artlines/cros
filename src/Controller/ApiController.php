<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends AbstractController
{
    /**
     * @Route("/api", name="api")
     */
    public function index(Request $request)
    {
        $host = $request->getHost();
        $conference = explode('.', $host)[0];
        return $this->render('default/door_'.$conference.'.html.twig', array(

        ));
    }

    /**
     * @Route("/api/conference")
     *
     * @return object
     */
    public function getConference(){
        $year = date('Y');
        $finish = date('Y-m-d H:i:s');

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT c
            FROM App:Conference c
            WHERE c.year = :year
            AND c.finish >= :finish
            ORDER BY c.finish DESC'
        )->setParameter('year', $year)->setParameter('finish', $finish);
        $conference = $query->getSingleResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($conference);

        return $response;
    }
}
