<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacyAction()
    {
        $content = file_get_contents('https://shop.nag.ru/policies/privacy');

        return $this->render('security/privacy.html.twig', array(
            'content' => $content,
        ));
    }


    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
            return $this->render('security/login_success.html.twig', array(
            ));
        }
        else {
            /** @var AuthenticationUtils $authenticationUtils */
            $authenticationUtils = $this->get('security.authentication_utils');

            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by user
            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->render('security/login.html.twig', array(
                'last_username' => $lastUsername,
                'error' => $error,
            ));
        }
    }
}