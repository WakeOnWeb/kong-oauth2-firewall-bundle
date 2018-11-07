<?php

namespace WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Factory;

use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Authentication\Provider\KongProvider;
use WakeOnWeb\Bundle\KongOAuth2FirewallBundle\Security\Firewall\KongListener;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Quentin Schuler <q.schuler@wakeonweb.com>
 */
class KongFactory implements SecurityFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = sprintf('security.authentication.provider.kong.%s', $id);

        $container
            ->setDefinition($providerId, new Definition(KongProvider::class, [
                new Reference($userProvider),
                $config['consumer_ids'],
                $config['anonymous_consumer_ids']
            ]))
        ;

        $listenerId = sprintf('security.authentication.listener.kong.%s', $id);

        $container->setDefinition($listenerId, new Definition(KongListener::class, [
            new Reference('security.token_storage'),
            new Reference('security.authentication.manager')
        ]));

        return [$providerId, $listenerId, $defaultEntryPoint];
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'http';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'kong';
    }

    /**
     * {@inheritdoc}
     *
     * @param ArrayNodeDefinition $builder
     */
    public function addConfiguration(NodeDefinition $builder)
    {
        $builder
            ->children()
                ->arrayNode('consumer_ids')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('anonymous_consumer_ids')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }
}
