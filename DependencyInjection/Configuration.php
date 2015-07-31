<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2015 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\SeoBundle\DependencyInjection;

use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Cmf\Bundle\SeoBundle\SeoPresentation;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges the configuration for the seo bundle.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $nodeBuilder = $treeBuilder->root('cmf_seo')
            ->addDefaultsIfNotSet()
            ->beforeNormalization()
                ->ifTrue(function ($config) {
                    return isset($config['sitemap'])
                        && (!isset($config['sitemap']['configurations'])
                            || 0 == count($config['sitemap']['configurations'])
                        )
                        && !isset($config['sitemap']['configuration']) // xml configuration
                    ;
                })
                ->then(function ($config) {
                    if (true === $config['sitemap']) {
                        $config['sitemap'] = array(
                            'enabled' => true,
                            'configurations' => array(
                                'sitemap' => array()
                            ),
                        );
                    } elseif (is_array($config['sitemap'])) {
                        $config['sitemap']['configurations'] = array('sitemap' => array());
                    }

                    return $config;
                })
            ->end()
            ->beforeNormalization()
                ->ifTrue(function ($config) {
                    return isset($config['content_key']) && !isset($config['content_listener']['content_key']);
                })
                ->then(function ($config) {
                    $config['content_listener']['content_key'] = $config['content_key'];
                    unset($config['content_key']);

                    return $config;
                })
            ->end()
            // validation needs to be on top, when no values are set a validation inside the content_listener array node will not be triggered
            ->validate()
                ->ifTrue(function ($v) { return $v['content_listener']['enabled'] && empty($v['content_listener']['content_key']); })
                ->thenInvalid('Configure the content_listener.content_key or disable the content_listener when not using the CmfRoutingBundle DynamicRouter.')
            ->end()
            ->children()
                ->scalarNode('translation_domain')->defaultValue('messages')->end()
                ->scalarNode('title')->end()
                ->scalarNode('description')->end()
                ->scalarNode('original_route_pattern')->defaultValue(SeoPresentation::ORIGINAL_URL_CANONICAL)->end()
       ;

        $this->addPersistenceSection($nodeBuilder);
        $this->addSonataAdminSection($nodeBuilder);
        $this->addAlternateLocaleSection($nodeBuilder);
        $this->addErrorHandlerSection($nodeBuilder);
        $this->addSitemapSection($nodeBuilder);
        $this->addContentListenerSection($nodeBuilder);

        return $treeBuilder;
    }

    /**
     * Attach the persistence node to the tree.
     *
     * @param NodeBuilder $treeBuilder
     */
    private function addPersistenceSection(NodeBuilder $treeBuilder)
    {
        $treeBuilder
            ->arrayNode('persistence')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('phpcr')
                        ->addDefaultsIfNotSet()
                        ->canBeEnabled()
                        ->children()
                            ->scalarNode('manager_name')->defaultNull()->end()
                        ->end()
                    ->end()

                    ->arrayNode('orm')
                        ->addDefaultsIfNotSet()
                        ->canBeEnabled()
                        ->children()
                            ->scalarNode('manager_name')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Attach the sonata admin node to the tree.
     *
     * @param NodeBuilder $treeBuilder
     */
    private function addSonataAdminSection(NodeBuilder $treeBuilder)
    {
        $treeBuilder
            ->arrayNode('sonata_admin_extension')
                ->addDefaultsIfNotSet()
                ->beforeNormalization()
                    ->ifTrue( function ($v) { return is_scalar($v); })
                    ->then(function ($v) {
                        return array('enabled' => $v);
                    })
                ->end()
                ->children()
                    ->enumNode('enabled')
                        ->values(array(true, false, 'auto'))
                        ->defaultValue('auto')
                    ->end()
                    ->scalarNode('form_group')->defaultValue('form.group_seo')->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Attach the alternate locale node to the tree.
     *
     * @param NodeBuilder $nodeBuilder
     */
    private function addAlternateLocaleSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('alternate_locale')
                ->addDefaultsIfNotSet()
                ->canBeEnabled()
                ->children()
                    ->scalarNode('provider_id')->defaultNull()->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Attach the error node to the tree.
     *
     * @param NodeBuilder $nodeBuilder
     */
    private function addErrorHandlerSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('error')
                ->children()
                    ->scalarNode('enable_parent_provider')->defaultValue(false)->end()
                    ->scalarNode('enable_sibling_provider')->defaultValue(false)->end()
                ->end()
            ->end()
        ;

    }

    /**
     * Attach the sitemap node to the tree.
     *
     * @param NodeBuilder $nodeBuilder
     */
    private function addSitemapSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('sitemap')
                ->fixXmlConfig('configuration')
                ->addDefaultsIfNotSet()
                ->canBeEnabled()
                ->children()
                    ->arrayNode('defaults')
                        ->fixXmlConfig('template')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('default_change_frequency')->defaultValue('always')->end()
                            ->arrayNode('templates')
                                ->useAttributeAsKey('format')
                                ->requiresAtLeastOneElement()
                                ->defaultValue(array(
                                    'html' => 'CmfSeoBundle:Sitemap:index.html.twig',
                                    'xml' => 'CmfSeoBundle:Sitemap:index.xml.twig',
                                ))
                                ->prototype('scalar')->end()
                            ->end()
                            ->append($this->getSitemapHelperNode('loaders', array('_all')))
                            ->append($this->getSitemapHelperNode('guessers', array('_all')))
                            ->append($this->getSitemapHelperNode('voters', array('_all')))
                        ->end()
                    ->end()
                    ->arrayNode('configurations')
                        ->useAttributeAsKey('name')
                        ->prototype('array')
                            ->fixXmlConfig('template')
                            ->fixXmlConfig('loader')
                            ->fixXmlConfig('guesser')
                            ->fixXmlConfig('voter')
                            ->children()
                                ->scalarNode('default_change_frequency')->defaultNull()->end()
                                ->arrayNode('templates')
                                    ->useAttributeAsKey('format')
                                    ->requiresAtLeastOneElement()
                                    ->prototype('scalar')->end()
                                ->end()
                                ->append($this->getSitemapHelperNode('loaders', array()))
                                ->append($this->getSitemapHelperNode('guessers', array()))
                                ->append($this->getSitemapHelperNode('voters', array()))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    private function getSitemapHelperNode($type, $default)
    {
        $node = new ArrayNodeDefinition($type);
        $node
            ->beforeNormalization()
                ->ifTrue(function ($config) {
                    return is_string($config);
                })
                ->then(function ($config) {
                    return array($config);
                })
            ->end()
            ->defaultValue($default)
            ->prototype('scalar')->end()
            ->end()
        ;

        return $node;
    }

    /**
     * Attach the content listener node to the tree.
     *
     * @param NodeBuilder $nodeBuilder
     */
    private function addContentListenerSection(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('content_listener')
                ->canBeDisabled()
                ->children()
                    ->scalarNode('content_key')
                    ->defaultValue(class_exists('Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter') ? DynamicRouter::CONTENT_KEY : '')
                ->end()
            ->end()
        ;
    }
}
