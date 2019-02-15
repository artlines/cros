<?php

namespace App\Security;

use App\Entity\Participating\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class GoogleAuthenticator extends SocialAuthenticator
{
    /** @var ClientRegistry */
    protected $clientRegistry;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var UrlGeneratorInterface */
    protected $router;

    /**
     * GoogleAuthenticator constructor.
     * @param ClientRegistry $clientRegistry
     * @param EntityManagerInterface $entityManager
     * @param UrlGeneratorInterface $router
     */
    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $router
    ) {
        $this->clientRegistry   = $clientRegistry;
        $this->em               = $entityManager;
        $this->router           = $router;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'google_auth_callback';
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse|Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('google_auth');
        return new RedirectResponse($url, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @return \League\OAuth2\Client\Token\AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGoogleClient());
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return User|null|\Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GoogleUser $googleUser */
        $googleUser = $this->getGoogleClient()->fetchUserFromToken($credentials);

        /** @var User|null $user */
        $user = $this->em->getRepository(User::class)
            ->findOneBy([
                'email'     => mb_strtolower($googleUser->getEmail()),
                'isActive'  => true,
            ]);

        return $user;
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return null|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $redirect_path = '/';

        if ($request->hasSession()) {
            $redirect_path = $request->getSession()->remove('redirect_after_auth') ?? '/';
        }

        return new RedirectResponse($redirect_path);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param Request $request
     * @param AuthenticationException $exception
     * @return null|Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @return \KnpU\OAuth2ClientBundle\Client\OAuth2Client|GoogleClient
     */
    private function getGoogleClient()
    {
        return $this->clientRegistry->getClient('google_nag');
    }
}