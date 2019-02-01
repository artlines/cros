<?php

namespace App\Controller\Site;

use App\Old\Entity\Conference;
use App\Entity\Lecture;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgramController extends AbstractController
{
    const DEFAULT_HALL = 'Зал Валдай';

    /**
     * @Route("/program", name="program")
     */
    public function program()
    {
        /** @var Conference $conf */
        $lectures = $this->getDoctrine()
            ->getRepository('App\Entity\Lecture')
            ->findBy([], ['date' => 'ASC', 'startTime' => 'ASC', 'endTime' => 'ASC']);

        $program = [];
        /** @var Lecture $lecture */
        foreach ($lectures as $lecture)
        {
            $_day_key = $lecture->getDate()->format('d.m.Y');
            $_time_key = $lecture->getStartTime()->format("H:i")." - ".$lecture->getEndTime()->format("H:i");
            $_hall_key = $lecture->getHall();

            if (!$lecture->getSpeaker() || !preg_match("/зал/", mb_strtolower($lecture->getHall()))) {
                // какой-то перерыв слеш кофе-брейк
                //$program[$_day_key][self::DEFAULT_HALL][$_time_key] = $lecture;
            } else {
                $program[$_day_key][$_hall_key][$_time_key] = $lecture;
            };
        }

        /**
         * Сортировка залов
         */
        $sortedProgram = [];
        foreach ($program as $day => $halls) {
            arsort($halls);
            $sortedProgram[$day] = $halls;
        }

        return $this->render('frontend/program/show_new2.html.twig', [
            'program' => $sortedProgram,
            'lectures' => $lectures
        ]);
    }
}
