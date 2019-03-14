<?php

namespace App\EventSubscriber;

use App\Entity\Participating\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IsUserActiveSubscriber implements EventSubscriberInterface
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var EntityManagerInterface */
    protected $em;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /**
     * IsUserActiveSubscriber constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $em
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Check that authorized user is active
     *
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param GetResponseEvent $event
     * @return null
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        /** If user is not User then skip */
        if (!$user instanceof User) {
            return null;
        }

        /** Fetch User from DB and check that it is active */
        $dbUser = $this->em->find(User::class, $user->getId());
        if ($dbUser && $dbUser->isActive() === TRUE) {
            return null;
        }

        /** Redirect to logout route */
        $event->setResponse(new RedirectResponse($this->urlGenerator->generate('logout')));

        return null;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}