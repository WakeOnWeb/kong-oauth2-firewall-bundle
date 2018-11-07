<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class KongUser implements UserInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @param string $username
     */
    public function __construct(string $username)
    {
        $this->username = $username;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {
        return ['ROLE_KONG_USER'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }
}
