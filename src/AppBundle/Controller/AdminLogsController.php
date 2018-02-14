<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Logs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminLogsController extends Controller
{
    /**
     * Вывод виджета логов
     *
     * @Route("/admin/logs_preview", name="admin-logs-preview")
     *
     * @return object
     */
    public function eventsAction()
    {
        /** @var Logs $events */
        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Logs')
            ->findBy(array(), array('date' => 'DESC'), 3);

        return $this->render('admin/logs/list.html.twig', array(
            'events' => $events,
        ));
    }

    /**
     * Вывод всех логов
     *
     * @Route("/admin/logs", name="admin-show-logs")
     */
    public function eventsListAction()
    {
        /** @var Logs $events */
        $events = $this->getDoctrine()
            ->getRepository('AppBundle:Logs')
            ->findBy(array(), array('date' => 'DESC'));

        return $this->render('admin/logs/full-list.html.twig', array(
            'events' => $events,
        ));
    }
}
