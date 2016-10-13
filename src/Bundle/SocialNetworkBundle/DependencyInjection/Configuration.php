<?php

/*
 * This file is part of KibokoSocialNetworkBundle.
 *
 * (c) Grégory Planchat <gregory@kiboko.fr>
 *
 * Thanks to Vincent GUERARD <v.guerard@fulgurio.net> for his work on FulgurioSocialNetworkBundle
 */

namespace Kiboko\Bundle\SocialNetworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kiboko_social_network');
        $this->addEmailSection($rootNode);
        $this->addRegistrationSection($rootNode);
        $this->addResettingSection($rootNode);
        $this->addConfirmationSection($rootNode);
        $this->addMessengerSection($rootNode);
        $this->addAvatarSection($rootNode);
        $this->addContactSection($rootNode);
        $this->addFriendsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Default email configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addEmailSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('from_email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')->defaultNull()->end()
                    ->end()
                    ->children()
                        ->scalarNode('sender_name')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Registration email configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addRegistrationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('registration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Registration:register_success_email.subject.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Registration:register_success_email.txt.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Registration:register_success_email.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Resetting email configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addResettingSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Resetting:reset_email.subject.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Resetting:reset_email.txt.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Resetting:reset_email.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Confirmation email configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addConfirmationSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('confirmation')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Registration:confirmation_email.subject.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Registration:confirmation_email.txt.twig')->end()
                            ->end()
                            ->children()
                                ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Registration:confirmation_email.html.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Messenger configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addMessengerSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('messenger')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('message')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('email')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('address')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('sender_name')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Messenger:message_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Messenger:message_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Messenger:message_email.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('answer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('email')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('address')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('sender_name')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Messenger:answer_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Messenger:answer_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Messenger:answer_email.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * User avatar configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addAvatarSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('avatar')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default')->defaultValue('bundles/kiboko_socialsocialnetwork/images/avatar.png')->end()
                    ->end()
                    ->children()
                        ->scalarNode('width')->defaultValue(50)->end()
                    ->end()
                    ->children()
                        ->scalarNode('height')->defaultValue(50)->end()
                    ->end()
                    ->children()
                        ->arrayNode('admin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('remove')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('email')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('address')->defaultNull()->end()
                                            ->end()
                                            ->children()
                                                ->scalarNode('sender_name')->defaultNull()->end()
                                            ->end()
                                            ->children()
                                                ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:remove-avatar_email.subject.twig')->end()
                                            ->end()
                                            ->children()
                                                ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:remove-avatar_email.txt.twig')->end()
                                            ->end()
                                            ->children()
                                                ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:remove-avatar_email.html.twig')->end()
                                            ->end()
                                            ->children()
                                                ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:remove-avatar_msn.html.twig')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Contact configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addContactSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('contact')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('admin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('email')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('address')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('sender_name')->defaultNull()->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:contact_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:contact_email.html.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:AdminUsers:contact_msn.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end();
    }

    /**
     * Admin friendship configuration.
     *
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addFriendsSection(ArrayNodeDefinition $node)
    {
        $node
                ->children()
                    ->arrayNode('friendship')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('nb_refusals')->defaultValue(3)->end()
                    ->end()
                    ->children()
                        ->arrayNode('email')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('address')->defaultNull()->end()
                            ->end()
                            ->children()
                                ->scalarNode('sender_name')->defaultNull()->end()
                            ->end()
                            ->children()
                                ->arrayNode('invit')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Friendship:invit_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Friendship:invit_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Friendship:invit_email.html.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:Friendship:invit_msn.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('accept')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Friendship:accept_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Friendship:accept_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Friendship:accept_email.html.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:Friendship:accept_msn.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('refuse')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Friendship:refuse_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Friendship:refuse_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Friendship:refuse_email.html.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:Friendship:refuse_msn.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->children()
                                ->arrayNode('remove')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('subject')->defaultValue('KibokoSocialNetworkBundle:Friendship:remove_email.subject.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('text')->defaultValue('KibokoSocialNetworkBundle:Friendship:remove_email.txt.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('html')->defaultValue('KibokoSocialNetworkBundle:Friendship:remove_email.html.twig')->end()
                                    ->end()
                                    ->children()
                                        ->scalarNode('msn')->defaultValue('KibokoSocialNetworkBundle:Friendship:remove_msn.html.twig')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ;
    }
}
