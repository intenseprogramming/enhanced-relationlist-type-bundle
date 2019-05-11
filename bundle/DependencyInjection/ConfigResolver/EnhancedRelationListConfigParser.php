<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-05-11 12:43 AM
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\ConfigResolver;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Class EnhancedRelationListConfigParser.
 *
 * @package   IntProg\EnhancedRelationListBundle\DependencyInjection\ConfigResolver
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class EnhancedRelationListConfigParser implements ParserInterface
{
    /**
     * Adds semantic configuration definition.
     *
     * @param NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('enhanced_relation_list')
                ->info('Settings related to the enhanced relation list field type.')
                ->children()
                    ->scalarNode('base_template')
                        ->info('The used base template.')
                    ->end()
                    ->arrayNode('attribute_templates')
                        ->info('Templates used for rendering the value of an relation attribute.')
                        ->example([
                            ['template' => '@ezdesign/template.html.twig', 'priority' => 0]
                        ])
                        ->prototype('array')
                            ->children()
                                ->scalarNode('template')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('priority')
                                    ->defaultValue(0)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('attribute_definition_templates')
                        ->info('Templates used for rendering the definition of an relation attribute.')
                        ->example([
                            ['template' => '@ezdesign/template.html.twig', 'priority' => 0]
                        ])
                        ->prototype('array')
                            ->children()
                                ->scalarNode('template')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('priority')
                                    ->defaultValue(0)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * Does semantic config to internal container parameters mapping for $currentScope.
     *
     * This method is called by the `ConfigurationProcessor`, for each available scopes (e.g. SiteAccess, SiteAccess
     * groups or "global").
     *
     * @param array                   $scopeSettings Parsed semantic configuration for current scope.
     *                                               It is passed by reference, making it possible to alter it for
     *                                               usage after `mapConfig()` has run.
     * @param string                  $currentScope
     * @param ContextualizerInterface $contextualizer
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (!isset($scopeSettings['enhanced_relation_list'])) {
            return;
        }

        if (isset($scopeSettings['enhanced_relation_list']['base_template'])) {
            $contextualizer->setContextualParameter(
                'enhanced_relation_list.base_template',
                $currentScope,
                $scopeSettings['enhanced_relation_list']['base_template']
            );
        }

        if (isset($scopeSettings['enhanced_relation_list']['attribute_templates'])) {
            $contextualizer->setContextualParameter(
                'enhanced_relation_list.attribute_templates',
                $currentScope,
                $scopeSettings['enhanced_relation_list']['attribute_templates']
            );
        }

        if (isset($scopeSettings['enhanced_relation_list']['attribute_definition_templates'])) {
            $contextualizer->setContextualParameter(
                'enhanced_relation_list.attribute_definition_templates',
                $currentScope,
                $scopeSettings['enhanced_relation_list']['attribute_definition_templates']
            );
        }
    }

    /**
     * This method is called by the ConfigurationProcessor before looping over available scopes.
     * You may here use $contextualizer->mapConfigArray().
     *
     * @param array                   $config Complete parsed semantic configuration
     * @param ContextualizerInterface $contextualizer
     */
    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
    }

    /**
     * This method is called by the ConfigurationProcessor after looping over available scopes.
     * You may here use $contextualizer->mapConfigArray().
     *
     * @param array                   $config Complete parsed semantic configuration
     * @param ContextualizerInterface $contextualizer
     */
    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
    }
}
