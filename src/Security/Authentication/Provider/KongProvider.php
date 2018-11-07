<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Authentication\Provider;

use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Authentication\KongToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class KongProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var string[]
     */
    private $consumerIds;

    /**
     * @var string[]
     */
    private $anonymousConsumerIds;

    /**
     * @param UserProviderInterface $userProvider
     * @param string[]              $consumerIds
     * @param string[]              $anonymousConsumerIds
     */
    public function __construct(UserProviderInterface $userProvider, array $consumerIds, array $anonymousConsumerIds)
    {
        $this->userProvider = $userProvider;
        $this->consumerIds = $consumerIds;
        $this->anonymousConsumerIds = $anonymousConsumerIds;
    }

    /**
     * {@inheritdoc}
     *
     * @param KongToken $token
     */
    public function authenticate(TokenInterface $token)
    {
        $authToken = null;

        if ($token->isAnonymous() && !in_array($token->getConsumerId(), $this->anonymousConsumerIds)) {
            throw new AuthenticationException('The submitted anonymous consumer ID is not in the whitelist.');
        } else if (!$token->isAnonymous() && !in_array($token->getConsumerId(), $this->consumerIds)) {
            throw new AuthenticationException('The submitted consumer ID is not in the whitelist.');
        }

        if ($token->isAnonymous()) {
            $authToken = new KongToken($token->getConsumerId(), ['IS_AUTHENTICATED_ANONYMOUSLY']);
        } else {
            $user = $this->userProvider->loadUserByUsername($token->getUsername());
            $roles = ['IS_AUTHENTICATED_FULLY'];

            if ($userRoles = $user->getRoles()) {
                if (!is_array($userRoles)) {
                    $userRoles = [$userRoles];
                }

                $roles = array_unique(array_merge($roles, $userRoles));
            }

            $authToken = new KongToken($token->getConsumerId(), $roles);
            $authToken->setUser($user);
        }

        return $authToken;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof KongToken;
    }
}
