<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle;

use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Factory\KongFactory;
use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\HeaderAuthenticator;
use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\TrustedUserProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class WakeOnWebKongOAuth2FirewallBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new KongFactory());
    }
}
