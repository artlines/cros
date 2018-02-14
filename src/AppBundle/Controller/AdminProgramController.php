<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Conference;
use AppBundle\Entity\Program;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\AppendText;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminProgramController extends Controller
{
    /**
     * Изменение текста выводимого перед вопросами
     *
     * @Route("/admin/program/text", name="admin-program-text")
     *
     * @param Request $request
     *
     * @return object
     */
    public function adminProgramTextAction(Request $request)
    {
        /** @var AppendText $program_text */
        $program_text = $this->getDoctrine()
            ->getRepository('AppBundle:AppendText')
            ->findOneBy(array('alias' => 'program'));

        /** @var Form $form */
        $form = $this->createFormBuilder($program_text)
            ->add('text', TextareaType::class, array('label' => 'Текст будет отображаться перед программой'))
            ->add('save', SubmitType::class, array('label' => 'Сохранить'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $program_text = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($program_text);
            $em->flush();

            $success = 'Текст сохранен';
        }

        return $this->render('admin/program/text.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Программа',
        ));
    }

    /**
     * Вывод списка номеров
     *
     * @Route("/admin/program/year/{year}/{day}", name="admin-program")
     * @Route("/admin/program/year/{year}")
     * @Route("/admin/program/year/{year}/")
     * @Route("/admin/program/year")
     * @Route("/admin/program/year/")
     *
     * @param integer|null $year
     * @param integer|null $day
     * @param Request $request
     *
     * @return object
     */
    public function programAction($year = null, $day = null, Request $request)
    {
        $result = false;

        if($year == null){
            return $this->redirectToRoute('admin-program', array('year' => date('Y')));
        }

        $session = $request->getSession();
        if($session->get('result')){
            $result = $session->get('result');
            $session->remove('result');
        }

        /** @var Conference $last_conf */
        $last_conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        /** @var Program $programs */
        $programs = $this->getDoctrine()
            ->getRepository('AppBundle:Program')
            ->findBy(array('conferenceId' => $last_conf->getId()), array('date' => 'ASC', 'start' => 'ASC'));

        $days = array();

        /** @var Program $program */
        foreach ($programs as $program){
            $days[$program->getDate()] = $program->getDate();
        }

        if($day != null){
            $programs = $this->getDoctrine()
                ->getRepository('AppBundle:Program')
                ->findBy(array('conferenceId' => $last_conf->getId(), 'date' => $day), array('date' => 'ASC', 'start' => 'ASC'));
        }

        return $this->render('admin/program/list.html.twig', array(
            'selectedconf' => $last_conf,
            'programs' => $programs,
            'result' => $result,
            'days' => $days,
            'year' => $year,
            'day' => $day,
        ));

    }

    /**
     * Редактирование и создание номеров
     *
     * @Route("/admin/program/{id}", name="admin-program-edit")
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return object
     */
    public function editProgramAction($id, Request $request)
    {
        $result = false;

        if($id == 'new'){
            $program = new Program();
            $new = true;
        }
        else {
            $program = $this->getDoctrine()
                ->getRepository('AppBundle:Program')
                ->find($id);
            if(!$program){
                return $this->redirectToRoute('admin-program-edit', array('id' => 'new'));
            }
            $new = false;
        }

        /** @var Form $form */
        $form = $this->createFormBuilder($program)
            ->add('title', TextType::class, array('label' => 'Event'))
            ->add('description', TextareaType::class, array('label' => 'Description'))
            ->add('date', TextType::class, array('label' => 'Date'))
            ->add('start', TextType::class, array('label' => 'Start'))
            ->add('end', TextType::class, array('label' => 'Finish'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($id == 'new'){
                $last_conf = $this->getDoctrine()
                    ->getRepository('AppBundle:Conference')
                    ->findOneBy(array('year' => date('Y')));
                $program->setConferenceId($last_conf->getId());
            }
            $program = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($program);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );

            if($id == 'new'){
                return $this->redirectToRoute('admin-program');
            }
        }

        return $this->render('admin/program/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Program',
            'new' => $new,
            'result' => $result,
        ));
    }

    /**
     * Удаление номера
     *
     * @Route("/admin/program/remove/{id}", name="admin-program-remove")
     * @param int $id
     * @param Request $request
     *
     * @return object
     */
    public function removeProgramAction($id, Request $request){
        /** @var Program $program */
        $program = $this->getDoctrine()
            ->getRepository('AppBundle:Program')
            ->find($id);
        if($program) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($program);
            $em->flush();
            $result = array(
                'status' => 'success',
                'text' => 'Удалено',
            );
        }
        else{
            $result = array(
                'status' => 'warning',
                'text' => 'Не удалось удалить элемент, возможно, он уже был удален',
            );
        }
        $session = $request->getSession();
        $session->set('result', $result);

        return $this->redirectToRoute('admin-program');
    }
}
