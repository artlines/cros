<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login")
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthorizationCheckerInterface $authorizationChecker, AuthenticationUtils $authenticationUtils)
    {
        if ($authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->render('security/login_success.html.twig');
        } else {
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('security/login.html.twig', [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }
    }

    /**
     * @Route("/auth", name="google_auth")
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     *
     * @param Request $request
     * @param ClientRegistry $clientRegistry
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function auth(Request $request, ClientRegistry $clientRegistry)
    {
        if ($request->hasSession()) {
            $request->getSession()->set('redirect_after_auth', $request->server->get('HTTP_REFERER'));
        }

        return $clientRegistry
            ->getClient('google_nag')
            ->redirect(
                ['email'],
                [
                    'prompt'        => 'select_account consent',
                    'redirect_uri'  => $this->generateUrl('google_auth_callback', [], UrlGeneratorInterface::ABSOLUTE_URL),
                ]
            );
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