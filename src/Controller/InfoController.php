<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Old\Entity\Info;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormError;

//use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;
//use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2 as RecaptchaTrue;

class InfoController extends AbstractController
{
    /**
     * @Route("/info/{alias}", name="info")
     * @param $alias
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function info($alias, Request $request, \Swift_Mailer $mailer)
	{
        /* SPONSOR */
        if ($alias == 'sponsors') {
            $goldenSponsors = $this->getDoctrine()->getRepository('App:Sponsor')
                ->findBy(['active' => true, 'type' => Sponsor::TYPE__GOLD]);
            $silverSponsors = $this->getDoctrine()->getRepository('App:Sponsor')
                ->findBy(['active' => true, 'type' => Sponsor::TYPE__SILVER]);

            return $this->render('frontend/info/show-sponsor.html.twig', [
                'golden' => $goldenSponsors,
                'silver' => $silverSponsors,
            ]);
        };

		/* ORGANIZE */
		if ($alias == 'organize') {
			$_dates = $this->getDoctrine()->getRepository('App:Conference')
					->findOneBy(array('year' => date("Y")));

			$dates = array(
					$_dates->getStart()->getTimestamp(),
					$_dates->getFinish()->getTimestamp()
				);

			return $this->render('frontend/info/organize.html.twig', ['dates' => $dates]);
		};

		/* BECOME-SPEAKER */
		if ($alias == 'become-speaker') {
			$good_extens = ['pdf', 'txt', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
			$mimeMsg = 'Допустимые расширения файлов: '.implode(', ', $good_extens);

            $defaultData = array(
                //'theses' => 'asd'
            );
			$form = $this->createFormBuilder($defaultData)
				->add('speaker', TextType::class, ['attr' => ['class' => 'cs-theme-color-gray-dark-v3'], 'label' => 'Имя'])
				->add('email', EmailType::class, ['attr' => ['class' => 'cs-theme-color-gray-dark-v3'], 'label' => 'E-mail'])
				->add('mobile', TextType::class, ['attr' => ['class' => 'cs-theme-color-gray-dark-v3'], 'label' => 'Контактный телефон'])
				->add('title', TextType::class, ['attr' => ['class' => 'cs-theme-color-gray-dark-v3'], 'label' => 'Тема доклада'])
                ->add('theses', TextareaType::class, ['attr' => ['class' => 'cs-theme-color-gray-dark-v3'], 'label' => 'Тезисы'])
                ->add('files', FileType::class, [
									'label' => 'Файлы', 
									'multiple' => true,
									'constraints' => [
										new Assert\All([
											new Assert\File(['maxSize' => '20M', 'mimeTypesMessage' => $mimeMsg]),
										]),
									],
				])
				->add('recaptcha', RecaptchaType::class, [
                    'constraints'       => [
                        new RecaptchaTrue(['message' => 'Обязательное поле'])
                    ],
                ])
                ->add('send', SubmitType::class, ['label' => 'Отправить', 'attr' => ['class' => 'btn-success']])
				->getForm();

			$form->handleRequest($request);

			/* check files extensions */
			$_files_valid = true;

			if ($form->isSubmitted()) {
				$files = $form->get('files')->getData();
				$_tmp = array();

				/** @var UploadedFile $file */
                foreach ($files as $file) {
					$_exten = $file->getClientOriginalExtension();
					if (!in_array($_exten, $good_extens)) {
						if ($_files_valid) {
							$_files_valid = false;
							$form->get('files')->addError(new FormError($mimeMsg));
						};
						break;
					};
				};
			};
			/* end check */

			if ($form->isSubmitted() && $form->isValid() && $_files_valid) {
                $data = $form->getData();

                $files = $data['files'];

                $message = new \Swift_Message();
                $message
                    ->setSubject('КРОС-2.0-18: Заявка на добавление докладчика')
                    ->setFrom('cros@nag.ru')
                    ->setTo($data['email'])
                    ->setBody(
                        $this->renderView(
                            'Emails/become-speaker.html.twig',
                            [
                                'speaker' => $data['speaker'],
                                'email' => $data['email'],
                                'mobile' => $data['mobile'],
                                'title' => $data['title'],
                                'theses' => $data['theses'],
                            ]
                        ), 'text/html');

                foreach ($files as $file) {
                    $message->attach(\Swift_Attachment::fromPath($file)
                            ->setFilename($file->getClientOriginalName()));
                }

                $mailer->send($message);

				return $this->render('frontend/info/become-speaker.html.twig', array(
					'form' => false,
					'data' => $data
		        ));
			}

			return $this->render('frontend/info/become-speaker.html.twig', array(
				'form' => $form->createView(),
				'data' => false
            ));
		};

		/* BECOME-SPONSOR */
		// (ФИО, Компания, Телефон, E-mail)
		if ($alias == 'become-sponsor') {

            $choices = array(
                'Золотой партнер' => 'Золотой партнер',
                'Серебрянный партнер' => 'Серебрянный партнер',
            );

			$defaultData = array(
				//'theses' => 'asd'
				);
			$form = $this->createFormBuilder($defaultData)
				->add('company', TextType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'Компания'))
				->add('email', EmailType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'E-mail'))
                ->add('mobile', TextType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'Контактный телефон'))
                ->add('packet', ChoiceType::class, array(
                    'attr' => array('class' => 'cs-theme-color-gray-dark-v3'),
                    'label' => 'Тип пакета',
                    'choices' => $choices,
                    'choice_attr' => array('Выберите тип пакета' => array('disabled' => '')),
                ))
                ->add('present', TextAreaType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'Что будет представлено'))
                ->add('recaptcha', RecaptchaType::class, [
                    'constraints' => [
                        new RecaptchaTrue(['message' => 'Обязательное поле'])
                    ],
                ])
                ->add('send', SubmitType::class, array('label' => 'Отправить', 'attr' => array('class' => 'btn-success')))
				->getForm();

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				$data = $form->getData();

                $message = new \Swift_Message();
                $message
                    ->setSubject('КРОС-2.0-18: Заявка на добавление спонсора')
                    ->setFrom('cros@nag.ru')
                    ->setTo('cros@nag.ru')
                    ->setBody(
                        $this->renderView(
                            'Emails/become-sponsor.html.twig',
                            array(
                                'email' => $data['email'],
                                'company' => $data['company'],
                                'mobile' => $data['mobile'],
                                'present' => $data['present'],
                                'packet' => $data['packet'],
                            )
                        ), 'text/html');

                $mailer->send($message);

				return $this->render('frontend/info/become-sponsor.html.twig', array(
					'form' => false,
					'data' => $data
		        ));
			}

			return $this->render('frontend/info/become-sponsor.html.twig', array(
				'form' => $form->createView(),
				'data' => false
            ));
		};

        /* reminder-SPONSOR */
        // task 49050
        // @Route("/info/{alias}", name="reminder")
        if ($alias == 'reminder') {
            $defaultData = array(
                //'theses' => 'asd'
            );
            $form = $this->createFormBuilder($defaultData)
                ->add('company', TextType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'Компания'))
                ->add('mobile', TextType::class, array('attr' => array('class' => 'cs-theme-color-gray-dark-v3'), 'label' => 'Контактный телефон'))
                ->add('recaptcha', RecaptchaType::class, [
                    'constraints' => [
                        new RecaptchaTrue(['message' => 'Обязательное поле'])
                    ],
                ])
                ->add('send', SubmitType::class, array('label' => 'Отправить', 'attr' => array('class' => 'btn-success')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $data = $form->getData();

                $message = new \Swift_Message();
                $message
                    ->setSubject('КРОС-2.0-18: Заявка на напоминание освобождения брони')
                    ->setFrom('cros@nag.ru')
                    ->setTo('cros@nag.ru')
                    ->setBody(
                        $this->renderView(
                            'Emails/reminder-sponsor.html.twig',
                            array(
                                'company' => $data['company'],
                                'mobile' => $data['mobile'],
                            )
                        ), 'text/html');

                $mailer->send($message);

                return $this->render('frontend/info/reminder-sponsor.html.twig', array(
                    'form' => false,
                    'data' => $data
                ));
            }

            return $this->render('frontend/info/reminder-sponsor.html.twig', array(
                'form' => $form->createView(),
                'data' => false
            ));
        };

        $info = null;

    	if(in_array($alias, ['place', 'result', 'terms', 'transfer', 'targets'])){
			$infoRepository = $this->getDoctrine()->getRepository('App:Info');
			$info = $infoRepository->findInfoByAlias($alias, date("Y"));
		}
		else{
	    		/** @var Info $info */
			$info = $this->getDoctrine()
		    		->getRepository('App:Info')
		    		->findOneBy(array('alias' => $alias), array('id' => 'DESC'));
		}
        
        if($info){
            return $this->render('frontend/info/show.html.twig', array(

                'info' => $info,
            ));
        }
        else{
            throw $this->createNotFoundException('Страница не найдена');
        }
    }
}
