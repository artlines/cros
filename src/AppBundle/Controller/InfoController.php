<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\Logs;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Organizations;
use AppBundle\Entity\OrgToConf;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use Symfony\Component\Form\FormError;

use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType as RecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;

class InfoController extends Controller
{
    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/test/{alias}", name="info_test")
     */
    public function testAction($alias){
		$infoRepository = $this->getDoctrine()->getRepository('AppBundle:Info');
		$info = $infoRepository->findInfoByAlias($alias, '2018');
		//$info1 = $infoRepository->find(118)->getConftoinfos();

		//var_dump($info);
		//var_dump($info1);
    }

    /**
     * @Route("/info/{alias}", name="info")
     */
    public function infoAction($alias, Request $request)
	{
		/* ORGANIZE */
		if ($alias == 'organize') {
			$_dates = $this->getDoctrine()->getRepository('AppBundle:Conference')
					->findOneBy(array('year' => date("Y")));

			$dates = array(
					$_dates->getStart()->getTimestamp(),
					$_dates->getFinish()->getTimestamp()
				);

			return $this->render('frontend/info/organize.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
				'dates' => $dates
            ));
		};

		/* BECOME-SPEAKER */
		// ФИО, Телефон, E-mail, Тема доклада, Тезисы, Приложить файл
		if ($alias == 'become-speaker') {
			$defaultData = array(
				//'theses' => 'asd'
				);
			$good_extens = array('pdf', 'txt', 'rtf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx');
			$mimeMsg = 'Допустимые расширения файлов: '.implode(', ', $good_extens);
			$form = $this->createFormBuilder($defaultData)
				->add('speaker', TextType::class, array('label' => 'ФИО'))
				->add('email', EmailType::class, array('label' => 'E-mail'))
				->add('mobile', TextType::class, array('label' => 'Телефон'))
				->add('title', TextType::class, array('label' => 'Тема доклада'))
                ->add('theses', TextareaType::class, array('label' => 'Тезисы'))
                ->add('files', FileType::class, array(
									'label' => 'Файлы', 
									'multiple' => true,
									'constraints' => array(
										new Assert\All(array(
											new Assert\File(array(
												'maxSize' => '20M',
												'mimeTypesMessage' => $mimeMsg
											)),
										)),
									),
				))
				->add('recaptcha', RecaptchaType::class, array(
									'label' => false, 
									'mapped' => false,
									'constraints' => array(
										new RecaptchaTrue(array(
											'message' => '',
										)),
									)
				))
                ->add('send', SubmitType::class, array('label' => 'Отправить'))
				->getForm();

			$form->handleRequest($request);

			/* check files extensions */
			$_files_valid = true;
			if ($form->isSubmitted()) {
				$files = $form->get('files')->getData();
				$_tmp = array();
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

					$message = \Swift_Message::newInstance()
                        ->setSubject('КРОС-2.0-18: Заявка на добавление докладчика')
                        ->setFrom('cros@nag.ru')
                        ->setTo('e.nachuychenko@nag.ru')
                        ->setBody(
                            $this->renderView(
                                'Emails/become-speaker.html.twig',
                                array(
                                    'speaker' => $data['speaker'],
                                    'email' => $data['email'],
                                    'mobile' => $data['mobile'],
                                    'title' => $data['title'],
                                    'theses' => $data['theses'],
                                )
                            ), 'text/html');

					foreach ($files as $file) {
						$message->attach(\Swift_Attachment::fromPath($file)
								->setFilename($file->getClientOriginalName()));
					}

                    $this->get('mailer')->send($message);

				return $this->render('frontend/info/become-speaker.html.twig', array(
		            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
					'form' => false,
					'data' => $data
		        ));
			}

			return $this->render('frontend/info/become-speaker.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
				'form' => $form->createView(),
				'data' => false
            ));
		};

		/* BECOME-SPONSOR */
		// (ФИО, Компания, Телефон, E-mail)
		if ($alias == 'become-sponsor') {
			$defaultData = array(
				//'theses' => 'asd'
				);
			$form = $this->createFormBuilder($defaultData)
				->add('fio', TextType::class, array('label' => 'ФИО'))
				->add('company', TextType::class, array('label' => 'Компания'))
				->add('email', EmailType::class, array('label' => 'E-mail'))
				->add('mobile', TextType::class, array('label' => 'Телефон'))
				->add('recaptcha', RecaptchaType::class, array(
									'label' => false, 
									'mapped' => false,
									'constraints' => array(
										new RecaptchaTrue(array(
											'message' => '',
										)),
									)
				))
                //->add('check', CheckboxType::class, array('label' => 'Я согласен на обработку <a href="https://shop.nag.ru/article/reg-oferta" target="_blank">персональных данных</a>'))
                ->add('send', SubmitType::class, array('label' => 'Отправить'))
				->getForm();

			$form->handleRequest($request);

			if ($form->isSubmitted() && $form->isValid()) {
				$data = $form->getData();

				//var_dump($form->get('recaptcha')); exit();

					$message = \Swift_Message::newInstance()
                        ->setSubject('КРОС-2.0-18: Заявка на добавление спонсора')
                        ->setFrom('cros@nag.ru')
                        ->setTo('e.nachuychenko@nag.ru')
                        ->setBody(
                            $this->renderView(
                                'Emails/become-sponsor.html.twig',
                                array(
                                    'fio' => $data['fio'],
                                    'email' => $data['email'],
                                    'company' => $data['company'],
                                    'mobile' => $data['mobile'],
                                )
                            ), 'text/html');

                    $this->get('mailer')->send($message);

				return $this->render('frontend/info/become-sponsor.html.twig', array(
		            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
					'form' => false,
					'data' => $data
		        ));
			}

			return $this->render('frontend/info/become-sponsor.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
				'form' => $form->createView(),
				'data' => false
            ));
		};
        
        $info = null;
    	if(in_array($alias, ['place', 'result', 'terms', 'transfer', 'targets'])){
			$infoRepository = $this->getDoctrine()->getRepository('AppBundle:Info');
			$info = $infoRepository->findInfoByAlias($alias, date("Y"));
		}
		else{
	    		/** @var Info $info */
			$info = $this->getDoctrine()
		    		->getRepository('AppBundle:Info')
		    		->findOneBy(array('alias' => $alias));
		}
        
        if($info){
            return $this->render('frontend/info/show.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'info' => $info,
            ));
        }
        else{
            throw $this->createNotFoundException('Страница не найдена');
        }
    }
}
