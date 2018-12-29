<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/privacy", name="privacy")
     */
    public function privacy()
    {
        $content = file_get_contents('https://shop.nag.ru/policies/privacy');

        return $this->render('security/privacy.html.twig', array(
            'content' => $content,
        ));
    }

    /**
     * @Route("/login", name="login")
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthorizationCheckerInterface $authorizationChecker, AuthenticationUtils $authenticationUtils)
    {
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('security/login_success.html.twig');
        } else {
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

    /**
     * @Route("/auth", name="auth")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function auth()
    {
        return $this->render();
    }
}