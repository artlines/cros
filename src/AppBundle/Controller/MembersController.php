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
use AppBundle\Entity\Setting;
use AppBundle\Entity\User;
use AppBundle\Entity\UserToApartament;
use AppBundle\Entity\UserToConf;
use AppBundle\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MembersController extends Controller
{

    /**
     * client
     */
    private $client = null;

    public function setUp(){
        $this->client = static::createClient();
    }

    /**
     * @Route("/members/logout", name="members-logout")
     *
     * @param Request $request
     * @return object
     */
    public function membersLogout(Request $request){
        $request->getSession()->invalidate();
        return $this->redirectToRoute('members');
    }

    /**
     * @Route("/gen-password", name="genpassword")
     *
     * @param string|boolean $pass
     * @param Request $request
     *
     * @return object
     */
    public function genPasswordAction(Request $request) {
        $email = $request->get('email');
        $password = $request->get('password');
        $UserRepository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        $user = $UserRepository->findOneBy(array('email' => $email));
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $password);
        return new Response($email . $password . '|' . $encoded);
    }

    
    /**
     * @Route("/members/{pass}", name="members")
     * @Route("/members")
     *
     * @param string|boolean $pass
     * @param Request $request
     *
     * @return object
     */
    public function membersAction($pass = false, Request $request)
    {
        $year = date("Y");
        $phone = false;
        $password = false;
        $session = $request->getSession();

        if($request->get('phone') != null){
            $phone = str_replace('+', '', $request->get('phone'));
            if($request->get('password') != null){
                $password = $request->get('password');
                /** @var UserRepository $UserRepository */
                $UserRepository = $this->getDoctrine()
                    ->getRepository('AppBundle:User');
                /** @var User $user */
                $user = $UserRepository->findOneBy(array('username' => $phone));
                if($user){
                    $encoder = $this->container->get('security.password_encoder');
                    $valid = $encoder->isPasswordValid($user, $password);
                    if($valid){
                        $session->set('sendauth', true);
                        $session->set('sendfrom', $user->getLastName().' '.$user->getFirstName().' из компании '.$user->getOrganization()->getName());
                    }
                }
            }
        }

        $send_auth = $session->get('sendauth');

        /** @var Conference $conf */
        $conf = $this->getDoctrine()
            ->getRepository('AppBundle:Conference')
            ->findOneBy(array('year' => $year));

        /** @var Organization $members */
        $members = $this->getDoctrine()
            ->getRepository('AppBundle:Organization')
            ->findAllByConference($conf->getId(), true);

        $show_list = true;

        // Получаем разрешенные даты регистрации
        $reg_start = $conf->getRegistrationStart();

        $now = date('Y-m-d H:i:s');

        if($now < $reg_start->format('Y-m-d H:i:s')){
            $show_list = false;
        }

        return $this->render('frontend/members/list.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'list' => $members,
            'showlist' => $show_list,
            'sendauth' => $send_auth,
        ));
    }
}
