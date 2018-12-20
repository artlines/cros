<?php

namespace App\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\AppendText;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminApartamentController extends AbstractController
{

    /**
     * Изменение текста выводимого перед вопросами
     *
     * @Route("/admin/price-text", name="admin-apartament-text")
     *
     * @param Request $request
     *
     * @return object
     */
    public function adminApartamentText(Request $request)
    {
        /** @var AppendText $apartament_text */
        $apartament_text = $this->getDoctrine()
            ->getRepository('App:AppendText')
            ->findOneBy(array('alias' => 'price'));

        /** @var Form $form */
        $form = $this->createFormBuilder($apartament_text)
            ->add('text', TextareaType::class, array('label' => 'Текст будет отображаться перед списком номеров проживания'))
            ->add('save', SubmitType::class, array('label' => 'Сохранить'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $apartament_text = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($apartament_text);
            $em->flush();

            $success = 'Текст сохранен';
        }

        return $this->render('admin/apartament/text.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Стоимость участия',
        ));
    }

    /**
     * Вывод списка номеров
     *
     * @Route("/admin/price/year/{year}", name="admin-price")
     * @Route("/admin/price/year")
     *
     * @param integer|null $year
     * @param Request $request
     *
     * @return object
     */
    public function price($year = null, Request $request)
    {
        $result = false;

        if($year == null){
            return $this->redirectToRoute('admin-price', array('year' => date('Y')));
        }

        $session = $request->getSession();
        if($session->get('result')){
            $result = $session->get('result');
            $session->remove('result');
        }

        $last_conf = $this->getDoctrine()
            ->getRepository('App:Conference')
            ->findOneBy(array('year' => $year));

        $apartament = $this->getDoctrine()
            ->getRepository('App:Apartament')
            ->findBy(array('conferenceId' => $last_conf->getId()));

        return $this->render('admin/apartament/list.html.twig', array(
            'selectedconf' => $last_conf,
            'apartaments' => $apartament,
            'result' => $result,
        ));

    }

    /**
     * Редактирование и создание номеров
     *
     * @Route("/admin/price/{id}", name="admin-apartament-edit")
     */
    public function editApartament($id, Request $request)
    {
        $result = false;

        if($id == 'new'){
            $apartament = new Apartament();
            $new = true;
        }
        else {
            $apartament = $this->getDoctrine()
                ->getRepository('App:Apartament')
                ->find($id);
            if(!$apartament){
                return $this->redirectToRoute('admin-apartament-edit', array('id' => 'new'));
            }
            $new = false;
        }

        /** @var Form $form */
        $form = $this->createFormBuilder($apartament)
            ->add('title', TextType::class, array('label' => 'Number title'))
            ->add('code', TextType::class , array('label' => 'Code'))
            ->add('description', TextareaType::class, array('label' => 'Description'))
            ->add('places', TextType::class, array('label' => 'Places'))
            ->add('price', TextType::class, array('label' => 'Price'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($id == 'new'){
                $last_conf = $this->getDoctrine()
                    ->getRepository('App:Conference')
                    ->findOneBy(array('year' => date('Y')));
                $apartament->setConferenceId($last_conf->getId());
            }
            /** @var Apartament $apartament */
            $apartament = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($apartament);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );

            if($id == 'new'){
                return $this->redirectToRoute('admin-price');
            }
        }

        return $this->render('admin/apartament/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Apartament',
            'new' => $new,
            'result' => $result,
        ));
    }

    /**
     * Удаление номера
     *
     * @Route("/admin/price/remove/{id}", name="admin-apartament-remove")
     * @param int $id
     * @param Request $request
     *
     * @return object
     */
    public function removeApartament($id, Request $request){
        /** @var Apartament $apartament */
        $apartament = $this->getDoctrine()
            ->getRepository('App:Apartament')
            ->find($id);
        if($apartament) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($apartament);
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

        return $this->redirectToRoute('admin-price');
    }
}
