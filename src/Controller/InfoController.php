<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Entity\Sponsor;
use App\Entity\Content\Info;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;
use Beelab\Recaptcha2Bundle\Form\Type\RecaptchaType;
use Beelab\Recaptcha2Bundle\Validator\Constraints\Recaptcha2 as RecaptchaTrue;

class InfoController extends AbstractController
{
    /**
     * @Route("/info/{alias}", name="info")
     * @param $alias
     * @param Request $request
     * @param Mailer $mailer
     * @param \Swift_Mailer $swiftMailer
     * @param EntityManagerInterface $em
     * @param ParameterBagInterface $parameterBag
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function info(
        $alias,
        Mailer $mailer,
        Request $request,
        \Swift_Mailer $swiftMailer,
        EntityManagerInterface $em,
        ParameterBagInterface $parameterBag
    ) {
        /* SPONSOR */
        if ($alias == 'sponsors') {
            /** @var Conference $conference */
            $conference = $em->getRepository(Conference::class)->findBy([], ['year' => 'DESC'], 1)[0];

            if (!$conference->getSponsors()->count()) {
                return $this->redirectToRoute('info', ['alias' => 'become-sponsor']);
            }

            $goldenSponsors = $em->getRepository('App:Sponsor')
                ->findBy(['active' => true, 'type' => Sponsor::TYPE__GOLD, 'conference' => $conference]);
            $silverSponsors = $em->getRepository('App:Sponsor')
                ->findBy(['active' => true, 'type' => Sponsor::TYPE__SILVER, 'conference' => $conference]);

            return $this->render('frontend/info/show-sponsor.html.twig', [
                'golden' => $goldenSponsors,
                'silver' => $silverSponsors,
            ]);
        };

		/* ORGANIZE */
		if ($alias == 'organize') {
		    /** @var Conference $conference */
			$conference = $em->getRepository('App:Conference')
					->findOneBy(array('year' => date("Y")));

			$dates = [
                $conference->getEventStart()->getTimestamp(),
                $conference->getEventFinish()->getTimestamp()
            ];

			return $this->render('frontend/info/organize.html.twig', ['dates' => $dates]);
		};

		/* BECOME-SPEAKER */
		if ($alias == 'become-speaker') {
            $bcc_emails = $parameterBag->has('become_speaker_bcc_emails')
                ? $parameterBag->get('become_speaker_bcc_emails') : [];

			$good_extens = ['pdf', 'txt', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
			$mimeMsg = 'Допустимые расширения файлов: '.implode(', ', $good_extens);

            $defaultData = [];
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

				/** @var UploadedFile $file */
                foreach ($files as $file) {
					$_exten = $file->getClientOriginalExtension();
					if (!in_array($_exten, $good_extens)) {
                        $_files_valid = false;
                        $form->get('files')->addError(new FormError($mimeMsg));
						break;
					}
				}
			}
			/* end check */

			if ($form->isSubmitted() && $form->isValid() && $_files_valid) {
                $data = $form->getData();

                $files = $data['files'];
                /** @var UploadedFile $file */
                foreach ($files as $file) {
                    $mailer->addAttachment($file->getPathname(), $file->getClientOriginalName());
                }

                $mailer->setTemplateAlias('cros2019.become_speaker');
                $mailer->send('КРОС-2019: Заявка на добавление докладчика', [
                    'speaker'   => $data['speaker'],
                    'email'     => $data['email'],
                    'mobile'    => $data['mobile'],
                    'title'     => $data['title'],
                    'theses'    => $data['theses'],
                ], $data['email'], null, $bcc_emails);

				return $this->render('frontend/info/become-speaker.html.twig', [
					'form' => false,
					'data' => $data
		        ]);
			}

			return $this->render('frontend/info/become-speaker.html.twig', [
				'form' => $form->createView(),
				'data' => false
            ]);
		};

		/* BECOME-SPONSOR */
		// (ФИО, Компания, Телефон, E-mail)
		if ($alias == 'become-sponsor') {
            $bcc_emails = $parameterBag->has('become_sponsor_bcc_emails')
                ? $parameterBag->get('become_sponsor_bcc_emails') : [];

            $choices = [
                'Золотой партнер'       => 'Золотой партнер',
                'Серебряный партнер'    => 'Серебряный партнер',
            ];

			$defaultData = [];
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
                ->add('send', SubmitType::class, ['label' => 'Отправить', 'attr' => ['class' => 'btn-success']])
				->getForm();

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {

				$data = $form->getData();

                $mailer->setTemplateAlias('cros2019.become_sponsor');
                $mailer->send('КРОС-2019: Заявка на добавление спонсора', [
                    'email'     => $data['email'],
                    'company'   => $data['company'],
                    'mobile'    => $data['mobile'],
                    'present'   => $data['present'],
                    'packet'    => $data['packet'],
                ], $data['email'], null, $bcc_emails);

				return $this->render('frontend/info/become-sponsor.html.twig', [
					'form' => false,
					'data' => $data
		        ]);
			}

			return $this->render('frontend/info/become-sponsor.html.twig', [
				'form' => $form->createView(),
				'data' => false
            ]);
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
                    ->setSubject('КРОС-2019: Заявка на напоминание освобождения брони')
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

                $swiftMailer->send($message);

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
    	if (in_array($alias, ['place', 'result', 'terms', 'transfer', 'targets'])) {
    	    /** @var Conference $conference */
			$conference = $em->getRepository('App:Conference')
                ->findOneBy(['year' => date("Y")]);
			/** @var Info $info */
			$info = $em->getRepository('App:Content\Info')
                ->findOneBy(['conference' => $conference, 'alias' => $alias]);
		} else {
            /** @var Info $info */
			$info = $em->getRepository('App:Content\Info')->findOneBy(['alias' => $alias]);
		}
        
        if ($info) {
            return $this->render('frontend/info/show.html.twig', ['info' => $info]);
        } else {
            throw $this->createNotFoundException('Страница не найдена');
        }
    }
}
