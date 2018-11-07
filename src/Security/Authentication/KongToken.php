<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class KongToken extends AbstractToken
{
    /**
     * @var string
     */
    private $consumerId;

    /**
     * @var bool
     */
    private $isAnonymous = true;

    /**
     * @param string   $consumerId
     * @param string[] $roles
     */
    public function __construct(string $consumerId, array $roles = [])
    {
        $this->consumerId = $consumerId;

        parent::__construct($roles);

        if (count($roles) > 0) {
            $this->setAuthenticated(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * @param bool $flag
     */
    public function setAnonymous(bool $flag): void
    {
        $this->isAnonymous = $flag;
    }

    /**
     * @return bool
     */
    public function isAnonymous(): bool
    {
        return $this->isAnonymous;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($user)
    {
        $this->isAnonymous = false;

        parent::setUser($user);
    }
    /**
     * @return string
     */
    public function getConsumerId(): string
    {
        return $this->consumerId;
    }
}
