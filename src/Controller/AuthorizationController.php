<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthorizationController extends AbstractController
{

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
     * @Route("/auth", name="google_auth")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @param ClientRegistry $clientRegistry
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function auth(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('google_nag')
            ->redirect([
                'email',
            ]);
    }

    /**
     * @Route("/auth/callback", name="google_auth_callback")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     */
    public function authCallback()
    {

    }
}