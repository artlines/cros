<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Apartament;
use AppBundle\Entity\ApartamentId;
use AppBundle\Entity\Conference;
use AppBundle\Entity\Info;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Logs;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
    /**
     * @Route("/profile/{page}", name="profile")
     * @Route("/profile")
     */
    public function indexAction($page = false, Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $result = false;

        if($page == 'security'){
            /** @var Form $form */
            $form = $this->createFormBuilder($user)
                ->add('password', PasswordType::class, array('label' => 'Password'))
                ->add('confirm_password', PasswordType::class, array('label' => 'Confirm password', 'mapped' => false))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user = $form->getData();

                $confirm_password = $form->get('confirm_password')->getData();

                if($user->getPassword() != null && $user->getPassword() != '') {

                    if ($confirm_password == $user->getPassword()) {
                        $encoder = $this->container->get('security.password_encoder');
                        $encoded = $encoder->encodePassword($user, $user->getPassword());

                        $user->setPassword($encoded);

                        $em->persist($user);
                        $em->flush();

                        return $this->redirectToRoute('profile');
                    } else {
                        $result = array(
                            'status' => 'danger',
                            'text' => 'пароли не совпадают',
                        );
                    }
                }
            }
            return $this->render('profile/security.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'form' => $form->createView(),
                'page' => $page,
            ));
        }
        else{
            if($user->getEntityName() == 'user'){
                $org = $this->getDoctrine()
                    ->getRepository('AppBundle:Organization')
                    ->find($user->getOrganizationId());
            }
            else{
                $org = $user;
            }

            /** @var Form $form */
            $form = $this->createFormBuilder($org)
                ->add('name', TextType::class, array('label' => 'Название организации', 'attr' => array('placeholder' => 'Ёлки-телеком', 'data-helper' => 'Ваш основной Торговый знак, будет использоваться на бейджах и визитках')))
                ->add('city', TextType::class, array('label' => 'City'))
                ->add('email', EmailType::class, array('label' => 'E-mail', 'required' => true, 'attr' => array('data-helper' => 'Для общих уведомлений, будет использоваться в качестве логина для доступа в личный кабинет')))
                ->add('username', TextType::class, array('label' => 'Телефон', 'attr' => array('data-helper' => 'Общий телефон для связи с Компанией', 'pattern' => '[\+][0-9]{11,}', 'title' => "Номер телефона в федеральном формате (+79990009999), без пробелов", 'placeholder' => '+79990009999')))
                ->add('inn', TextType::class, array('label' => 'ИНН', 'required' => true))
                ->add('kpp', TextType::class, array('label' => 'КПП', 'required' => true))
                ->add('requisites', TextareaType::class, array('label' => 'Реквизиты', 'attr' => array('data-helper' => 'Для выставления счета')))
                ->add('address', TextareaType::class, array('label' => 'Address', 'required' => false))
                ->add('comment', TextareaType::class, array('label' => 'Комментарий', 'required' => false))
                ->add('save', SubmitType::class, array('label' => 'Save'))
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $org = $form->getData();

                $em->persist($org);
                $em->flush();

                return $this->redirectToRoute('registration-3');
            }

            return $this->render('profile/info.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
                'form' => $form->createView(),
                'result' => $result,
            ));
        }
    }

    /**
     * @Route("/profile/members/remove/{id}", name="profile-member-remove")
     *
     * @param mixed $id
     * @param Request $request
     *
     * @return object
     */
    public function removeAction($id, Request $request){
        $em = $this->getDoctrine()->getManager();

        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($id);
        $utcs = $user->getUtocs();
        foreach($utcs as $utc){
            $em->remove($utc);
            $em->flush();
        }
        $utas = $user->getUtoas();
        foreach ($utas as $uta){
            $em->remove($uta);
            $em->flush();
        }

        $org = $user->getOrganization();

        $user->setIsActive(0);

        $message = \Swift_Message::newInstance()
            ->setSubject('Регистрация КРОС-2.0-17: ' . $org->getName().' - '.$user->getLastName().' '.$user->getFirstName())
            ->setFrom('cros@nag.ru')
            ->setTo($user->getEmail())
            ->setBcc(array('xvanok@nag.ru', 'cros@nag.ru', 'esuzev@nag.ru'))
            ->setBody(
                $this->renderView(
                    'Emails/remove_user.html.twig',
                    array(
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('registration-3');
    }

}
