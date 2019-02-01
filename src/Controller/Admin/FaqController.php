<?php

namespace App\Controller\Admin;

use AppBundle\Entity\Faq;
use AppBundle\Entity\AppendText;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FaqController extends AbstractController
{
    /**
     * Изменение текста выводимого перед вопросами
     *
     * @Route("/admin/faq/text", name="admin-faq-text")
     *
     * @param Request $request
     *
     * @return object
     */
    public function adminFaqText(Request $request)
    {
        /** @var AppendText $faq_text */
        $faq_text = $this->getDoctrine()
            ->getRepository('App:AppendText')
            ->findOneBy(array('alias' => 'faq'));

        /** @var Form $form */
        $form = $this->createFormBuilder($faq_text)
            ->add('text', TextareaType::class, array('label' => 'This text will be displayed before questions and answers list'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $faq_text = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($faq_text);
            $em->flush();

            $success = 'Текст сохранен';
        }

        return $this->render('admin/faq/text.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'FAQ',
        ));
    }

    /**
     * Вывод списка вопросов
     *
     * @Route("/admin/faq", name="admin-faq")
     *
     * @param Request $request
     *
     * @return object
     */
    public function faq(Request $request)
    {
        $result = false;

        $session = $request->getSession();
        if($session->get('result')){
            $result = $session->get('result');
            $session->remove('result');
        }

        /** @var Faq $faq */
        $faq = $this->getDoctrine()
            ->getRepository('App:Faq')
            ->findAll();

        return $this->render('admin/faq/list.html.twig', array(
            'faq' => $faq,
            'result' => $result,
        ));

    }

    /**
     * Редактирование и создание вопросов
     *
     * @Route("/admin/faq/{id}", name="admin-faq-edit")
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return object
     */
    public function editFaq($id, Request $request)
    {
        $result = false;

        if($id == 'new'){
            $faq = new Faq();
            $new = true;
        }
        else {
            /** @var Faq $faq */
            $faq = $this->getDoctrine()
                ->getRepository('App:Faq')
                ->find($id);
            if(!$faq){
                return $this->redirectToRoute('admin-faq-edit', array('id' => 'new'));
            }
            $new = false;
        }

        /** @var Form $form */
        $form = $this->createFormBuilder($faq)
            ->add('isActive', CheckboxType::class, array('label' => 'On', 'required' => false))
            ->add('question', TextareaType::class, array('label' => 'Question'))
            ->add('answer', TextareaType::class, array('label' => 'Answer'))
            ->add('save', SubmitType::class, array('label' => 'Save'))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $faq = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );

            if($id == 'new'){
                return $this->redirectToRoute('admin-faq-edit', array('id' => $faq->getId()));
            }
        }

        return $this->render('admin/faq/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'FAQ',
            'new' => $new,
            'result' => $result,
        ));
    }

    /**
     * Удаление вопроса
     *
     * @Route("/admin/faq/remove/{id}", name="admin-faq-remove")
     *
     * @param integer $id
     * @param Request $request
     *
     * @return object
     */
    public function removeFaq($id, Request $request){
        /** @var Faq $faq */
        $faq = $this->getDoctrine()
            ->getRepository('App:Faq')
            ->find($id);
        if($faq) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
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

        return $this->redirectToRoute('admin-faq');
    }
}
