<?php

namespace App\Controller;

use App\Old\Entity\Conference;
use App\Old\Entity\Interview;
use App\Repository\ConferenceRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/rate", name="rate-form")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ratePage()
    {
        return $this->render('rate/index.html.twig');
    }

    /**
     * @Route("/thankyou", name="end-interview")
     * @param Request $request
     * @return object
     */
    public function interviewend()
    {
        return $this->render('interview/interviewend.twig',array());
    }
    /**
     * @Route("/oops", name="error-interview")
     * @param Request $request
     * @return object
     */
    public function interviewerr()
    {
        return $this->render('interview/interviewerr.twig',array());
    }
    /**
     * @Route("/interview", name="interview")
     * @param Request $request
     * @return object
     */
    public function interview(Request $request)
    {
        $orgsts = $this->getDoctrine()->getRepository('App:Organization');
        $orgsts = $orgsts->findBy(array(), array('id' => 'ASC'));
        foreach ($orgsts as $org){
            $boxOrgsts[$org->getName()] = $org->getId();
        }
        $form = $this->createFormBuilder()
            ->add('organization', ChoiceType::class, array(
                'label' => 'Организация',
                'choices'  => $boxOrgsts))
            ->add('name', TextType::class, array('label' => '2. Ваше имя'))
            ->add('visits', ChoiceType::class, array(
                'label' => '3. Сколько раз Вы посещали КРОС?',
                'choices'  => array(
                    '1'=>'1',
                    '2'=>'2',
                    '3'=>'3',
                    '4'=>'4',
                    '5'=>'5',
                    '6'=>'6',
                    '7'=>'7',
                    '8'=>'8',
                    '9'=>'9',
                    '10'=>'10',
                    '11'=>'11',
                    '12'=>'12',
                    '13'=>'13',
                )))
            ->add('QualityOrganization', ChoiceType::class, array(
                'label' => '4. Оцените, пожалуйста, качество организации и проведения КРОС 2.0-18 в целом',
                'choices'  => array(
                    'Есть над чем работать'=>'1',
                    'Нормально'=>'2',
                    'Хорошо'=>'3',
                    'На высшем уровне'=>'4',
                )))
            ->add('QualityOrganizationComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
                ))
            ->add('Presentations',ChoiceType::class,
                array(
                    'label' => '5. Оцените, пожалуйста, актуальность и качество подготовки презентаций/докладов',
                    'choices' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                     '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('PresentationsComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий, можно описать свои впечатления, в том числе, и по сравнению с прошлыми годами. ',
            ))
            ->add('tables',ChoiceType::class,
                array(
                    'label' => '6. Оцените, пожалуйста, актуальность и качество проведения круглых столов',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('tablesComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('Entertainment',ChoiceType::class,
                array(
                    'label' => '7. Оцените развлекательную часть мероприятия',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('EntertainmentComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Какие развлечения вы хотели бы увидеть в следующем году?',
            ))
            ->add('Food', ChoiceType::class, array(
                'label' => '8. Оцените, пожалуйста, качество организации питания: кухню и сервис',
                'choices'  => array(
                    'Организация питания оставляет желать лучшего'=>'Организация питания оставляет желать лучшего',
                    'Нормально, но время организовано неудачно'=>'Нормально, но время организовано неудачно',
                    'Хорошая кухня, удобный график'=>'Хорошая кухня, удобный график',
                    'Питание организовано идеально, ничего менять не требуется'=>'Питание организовано идеально, ничего менять не требуется',
                )))
            ->add('FoodComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('Search',ChoiceType::class,
                array(
                    'label' => '9. Оцените информационное сопровождение конференции: удобство и своевременность получения информации, возможность связаться с коллегами, подсказки об изменении в расписании. В комментариях можно предложить варианты улучшения информационного обмена.',
                    'choices' => array(
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'),
                    'choices_as_values' => true,'multiple'=>false,'expanded'=>true))
            ->add('SearchComents', TextareaType::class, array(
                'required' => false,
                'label' => 'Ваш комментарий',
            ))
            ->add('WhatImportantComent', TextareaType::class, array(
                'required' => false,
                'label' => '10. Чего вам не хватило, чтобы ощутить себя на идеально организованной провайдерской конференции?',
            ))
            ->add('save', SubmitType::class,array('label' => 'Отправить ответы') )
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $Interview = new Interview();
            $em = $this->getDoctrine()->getManager();
            $form = $form->getData();

            $Interview->setName($form['name']);
            if($form['organization'] != null) {
                $orgsts = $this->getDoctrine()->getRepository('App:Organization');
                $organization = $orgsts->find($form['organization']);
                $Interview->setCompany($organization);
            }else{
                return $this->redirectToRoute('error-interview');
            }
            $Interview->setVisits($form['visits']);
            $Interview->setQualityOrganization($form['QualityOrganization']);
            //$Interview->setQualityOrganizationComents($form['QualityOrganizationComents']);
            $Interview->setPresentations($form['Presentations']);
            $Interview->setTables($form['tables']);
            $Interview->setEntertainment($form['Entertainment']);
            $Interview->setFood($form['Food']);
            $Interview->setSearch($form['Search']);
            //$Interview->setInformationalResources($form['InformationalResources']);
            //$Interview->setWhatImportant(1);

            if($form['QualityOrganizationComents'] != null){
                $Interview->setQualityOrganizationComents($form['QualityOrganizationComents']);
            }
            if($form['PresentationsComents'] != null){
                $Interview->setPresentationsComents($form['PresentationsComents']);
            }
            if($form['tablesComents'] != null){
                $Interview->setTablesComents($form['tablesComents']);
            }
            if($form['EntertainmentComents'] != null){
                $Interview->setEntertainmentComents($form['EntertainmentComents']);
            }
            if($form['FoodComents'] != null){
                $Interview->setFoodComents($form['FoodComents']);
            }
            if($form['SearchComents'] != null){
                $Interview->setSearchComents($form['SearchComents']);
            }
            /*
            if($form['InformationalResourcesComents'] != null){
                $Interview->setInformationalResourcesComents($form['InformationalResourcesComents']);
            }
            */
            if($form['WhatImportantComent'] != null){
                $Interview->setWhatImportantComent($form['WhatImportantComent']);
            }
            /*
            if($form['WhatImportant'] != null){
                $shops = implode(';', $form['WhatImportant']);
                $Interview->setWhatImportant($shops);
            }
            */
            $em->persist($Interview);
            $em->flush();

            return $this->redirectToRoute('end-interview');
        }

        return $this->render('interview/interview.twig',array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/footer", name="footer")
     */
    public function footer()
    {
        return $this->render('default/footer.html.twig');
    }
}
