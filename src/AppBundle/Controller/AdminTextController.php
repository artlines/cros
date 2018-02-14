<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Info;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminTextController extends Controller
{
    /**
     * Вывод списка текстовых страниц
     *
     * @Route("/admin/text", name="admin-text")
     *
     * @param Request $request
     *
     * @return object
     */
    public function textAction(Request $request)
    {
        $result = false;

        $session = $request->getSession();
        if($session->get('result')){
            $result = $session->get('result');
            $session->remove('result');
        }

        $info = $this->getDoctrine()
            ->getRepository('AppBundle:Info')
            ->findAllFree();

        return $this->render('admin/info/list.html.twig', array(
            'info' => $info,
            'result' => $result,
        ));

    }

    /**
     * Редактирование и создание текстовых страниц
     *
     * @Route("/admin/text/{id}", name="admin-text-edit")
     *
     * @param mixed $id
     * @param Request $request
     * @return object
     */
    public function editTextAction($id, Request $request)
    {
        $result = false;

        if($id == 'new'){
            $info = new Info();
            $new = true;
        }
        else {
            $info = $this->getDoctrine()
                ->getRepository('AppBundle:Info')
                ->find($id);
            if(!$info){
                return $this->redirectToRoute('admin-text-edit', array('id' => 'new'));
            }
            $new = false;
        }
        $aliases = array(
            'targets',
            'place',
            'transfer',
            'terms',
            'result',
            'sponsors',
        );

        if(in_array($info->getAlias(),  $aliases)){
            /** @var Form $form */
            $form = $this->createFormBuilder($info)
                ->add('title', TextType::class, array('label' => 'Title'))
                ->add('content', TextareaType::class, array('label' => 'Text'))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();
        }
        else {
            /** @var Form $form */
            $form = $this->createFormBuilder($info)
                ->add('alias', TextType::class, array('label' => 'Alias'))
                ->add('title', TextType::class, array('label' => 'Title'))
                ->add('content', TextareaType::class, array('label' => 'Text'))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();
        }

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $info = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($info);
            $em->flush();

            $result = array(
                'status' => 'success',
                'text' => 'Сохранено',
            );

            if($id == 'new'){
                return $this->redirectToRoute('admin-text-edit', array('id' => $info->getId()));
            }
        }

        return $this->render('admin/info/edit.html.twig', array(
            'form' => $form->createView(),
            'h1' => 'Текстовые страницы',
            'new' => $new,
            'result' => $result,
        ));
    }

    /**
     * Удаление вопроса
     *
     * @Route("/admin/text/remove/{id}", name="admin-text-remove")
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return object
     */
    public function removeInfoAction($id, Request $request){
        /** @var Info $info */
        $info = $this->getDoctrine()
            ->getRepository('AppBundle:Info')
            ->find($id);
        if($info) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($info);
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

        return $this->redirectToRoute('admin-history');
    }
}
