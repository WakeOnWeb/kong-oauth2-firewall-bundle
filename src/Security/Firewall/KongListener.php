<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Firewall;

use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Authentication\KongToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class KongListener implements ListenerInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthenticationManagerInterface
     */
    private $authenticationManager;

    /**
     * @param TokenStorageInterface          $tokenStorage
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthenticationManagerInterface $authenticationManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $anonymous = $request->headers->has('X-Anonymous-Consumer');
        $consumerId = $request->headers->get('X-Consumer-ID', $request->headers->get('X-Anonymous-Consumer'));
        $userId = $request->headers->get('X-Authenticated-UserID');

        if ($consumerId === null) {
            $event->setResponse(new Response('', Response::HTTP_FORBIDDEN));

            return;
        }

        $token = new KongToken($consumerId);

        if ($anonymous === true) {
            $token->setAnonymous(true);
        } else {
            if ($userId === null) {
                $event->setResponse(new Response('', Response::HTTP_FORBIDDEN));

                return;
            }

            $token->setAnonymous(false);
            $token->setUser($userId);
        }

        try {
            $this->tokenStorage->setToken($this->authenticationManager->authenticate($token));

            return;
        } catch (AuthenticationException $e) {
        }

        $event->setResponse(new Response('', Response::HTTP_FORBIDDEN));
    }
}
