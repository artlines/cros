<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @Route("/api", name="api")
     */
    public function indexAction(Request $request)
    {
        $host = $request->getHost();
        $conference = explode('.', $host)[0];
        return $this->render('default/door_'.$conference.'.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/api/conference")
     *
     * @return object
     */
    public function getConferenceAction(){
        $year = date('Y');
        $finish = date('Y-m-d H:i:s');

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT c
            FROM AppBundle:Conference c
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
