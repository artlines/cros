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


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }


    /**
     * @Route("/", name="homepage")
     */
    public function newMainAction()
    {
        return $this->render('cros2/base.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ));
    }

    /**
     * @Route("/old", name="cros-old")
     */
    public function indexAction()
    {
        $monthes = array(
            '01' => 'января',
            '02' => 'февраля',
            '03' => 'марта',
            '04' => 'апреля',
            '05' => 'мая',
            '06' => 'июня',
            '07' => 'июля',
            '08' => 'августа',
            '09' => 'сентября',
            '10' => 'октября',
            '11' => 'ноября',
            '12' => 'декабря'
        );

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => date('Y')));
        $event_date = $conf->getStart()->format('m');
        $sday = $conf->getStart()->format('d');
        $eday = $conf->getFinish()->format('d');
        $year= $conf->getStart()->format('Y');
        $month = $monthes[$event_date];

        return $this->render('default/door.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'month' => $month,
            'sday' => $sday,
            'eday' => $eday,
            'year' => $year,
        ));
    }

    /**
     * @Route("/footer", name="footer")
     */
    public function footerAction()
    {
        /** @var Setting $settings */
        $settings = $this->getDoctrine()
            ->getRepository('AppBundle:Setting')
            ->find(1);

        $footer_text = $settings->getFooterText();

        return $this->render('default/footer.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'footer_text' => $footer_text,
        ));
    }

    /**
     */
    public function countdownAction()
    {
		$reg_time = $this->getDoctrine()->getRepository('AppBundle:Conference')
					->findOneBy(array('year' => date("Y")));

		$reg_start = $reg_time->getRegistrationStart()->getTimestamp();
		$reg_finish = $reg_time->getRegistrationFinish()->getTimestamp();
		$now = time();

		$countdown_date = false;
		$text = false;
		if ($now < $reg_start) {
			// before reg
			$countdown_date = $reg_start;
			$text = "До начала регистрации";
		} elseif ($now > $reg_start && $now < $reg_finish) {
			// regtime
			$countdown_date = $reg_finish;
			$text = "До конца регистрации";
		} elseif ($now > $reg_finish) {
			// after reg
			$text = "Регистрация окончена";
		};
		
		return $this->render('default/countdown.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
			'countdown_date' => $countdown_date,
			'text' => $text
        ));
	}

	/**
     * @Route("/pre-reg", name="pre-reg")
     */
	public function preRegAction(Request $request)
    {
        $_input_class = 'form-control cs-font-size-13 cs-theme-color-dark-grey-v2 cs-placeholder-inherit cs-bg-light-opacity-0_8 cs-bg-light-v1--focus cs-brd-none rounded-0 cs-pa-20';
        $form = $this->createFormBuilder()
            ->add('fio', TextType::class, array(
                'attr' => array(
                    'placeholder' => '* Фамилия, Имя',
                    'class' => $_input_class
                ),
                'label' => false,
            ))
            ->add('company', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Компания',
                    'class' => $_input_class
                ),
                'label' => false,
                'required' => false
            ))
            ->add('position', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Должность',
                    'class' => $_input_class
                ),
                'label' => false,
                'required' => false
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'placeholder' => '* E-mail',
                    'class' => $_input_class
                ),
                'label' => false
            ))
            ->add('mobile', TextType::class, array(
                'attr' => array(
                    'placeholder' => '* Контактный телефон',
                    'class' => $_input_class
                ),
                'label' => false
            ))
            ->add('pd_pk_accept', CheckboxType::class, array(
                'label'    => 'Согласие на обработку ПД, согласие с ПК'
            ))
            ->add('send', SubmitType::class, array(
                'label' => 'Подать заявку',
                'attr' => array(
                    'class' => 'btn u-btn-primary btn-lg text-uppercase cs-font-weight-700 cs-font-size-12 rounded-0 cs-px-20 cs-py-20 mb-0',
                ),
            ))
            ->getForm();


        if ($request->isMethod('AJAX')) {

            /**
             * TODO: Here manual submit form
             */
            //$form->submit($request->request->get($form->getName()));


            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $message = \Swift_Message::newInstance()
                    ->setSubject('КРОС-2.0-18: Заявка на становление участником')
                    ->setFrom('cros@nag.ru')
                    ->setTo('e.nachuychenko@nag.ru')
                    ->setBody(
                        $this->renderView(
                            'Emails/become-member.html.twig',
                            array(
                                'fio' => $data['fio'],
                                'email' => $data['email'],
                                'company' => $data['company'],
                                'mobile' => $data['mobile'],
                                'position' => $data['position'],
                            )
                        ), 'text/html');

                $arrResult = array(
                    'success' => true
                );

                if ($this->get('mailer')->send($message))
                {
                    $arrResult['response'] = 'Заявка отправлена';
                }
                else
                {
                    $arrResult['response'] = 'В данный момент отправка заявки невозможна.';
                    $arrResult['success'] = false;
                }

                return new JsonResponse($arrResult);
            }
        }

        return $this->render('cros2/_form/_become-member.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'form' => $form->createView()
        ));
    }
}
