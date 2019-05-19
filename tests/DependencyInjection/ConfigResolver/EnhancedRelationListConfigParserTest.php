<?php

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\Contextualizer;
use IntProg\EnhancedRelationListBundle\DependencyInjection\ConfigResolver\EnhancedRelationListConfigParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Processor;

class EnhancedRelationListConfigParserTest extends TestCase
{
    public function testAddSemanticConfig()
    {
        $processor    = new Processor();
        $configParser = new EnhancedRelationListConfigParser();

        $nodeBuilder    = new NodeBuilder();
        $nodeDefinition = new ArrayNodeDefinition('test_node');
        $nodeBuilder->setParent($nodeDefinition);

        $configParser->addSemanticConfig($nodeBuilder);

        $processed = $processor->process($nodeDefinition->getNode(true), [
            [
                'enhanced_relation_list' => [
                    'base_template'       => 'the first base template',
                    'attribute_templates' => [
                        ['template' => 'first test template'],
                    ],
                ],
            ],
            [
                'enhanced_relation_list' => [
                    'attribute_templates'            => [
                        ['template' => 'second test template', 'priority' => 2],
                    ],
                    'attribute_definition_templates' => [
                        ['template' => 'third test template'],
                    ],
                ],
            ],
            [
                'enhanced_relation_list' => [
                    'base_template'                  => 'the second base template',
                    'attribute_definition_templates' => [
                        ['template' => 'fourth test template'],
                    ],
                ],
            ],
        ]);

        $this->assertEquals([
            'enhanced_relation_list' => [
                'base_template'                  => 'the second base template',
                'attribute_templates'            => [
                    ['template' => 'first test template', 'priority' => 0],
                    ['template' => 'second test template', 'priority' => 2],
                ],
                'attribute_definition_templates' => [
                    ['template' => 'third test template', 'priority' => 0],
                    ['template' => 'fourth test template', 'priority' => 0],
                ],
            ],
        ], $processed);
    }

    public function testMapConfig()
    {
        $contextualizer = $this->createMock(Contextualizer::class);
        $contextualizer->expects($this->at(0))->method('setContextualParameter')->with(
            'enhanced_relation_list.base_template',
            'current_scope',
            'the second base template'
        );
        $contextualizer->expects($this->at(1))->method('setContextualParameter')->with(
            'enhanced_relation_list.attribute_templates',
            'current_scope',
            [
                ['template' => 'first test template', 'priority' => 0],
                ['template' => 'second test template', 'priority' => 2],
            ]
        );
        $contextualizer->expects($this->at(2))->method('setContextualParameter')->with(
            'enhanced_relation_list.attribute_definition_templates',
            'current_scope',
            [
                ['template' => 'third test template', 'priority' => 0],
                ['template' => 'fourth test template', 'priority' => 0],
            ]
        );

        $configParser  = new EnhancedRelationListConfigParser();
        $scopeSettings = [
            'enhanced_relation_list' => [
                'base_template'                  => 'the second base template',
                'attribute_templates'            => [
                    ['template' => 'first test template', 'priority' => 0],
                    ['template' => 'second test template', 'priority' => 2],
                ],
                'attribute_definition_templates' => [
                    ['template' => 'third test template', 'priority' => 0],
                    ['template' => 'fourth test template', 'priority' => 0],
                ],
            ],
        ];
        $configParser->mapConfig($scopeSettings, 'current_scope', $contextualizer);

        $emptySettings = [];
        $configParser->mapConfig($emptySettings, 'current_scope', $contextualizer);
    }

    public function testPreMap()
    {
        $contextualizer = $this->createMock(Contextualizer::class);
        $configParser   = new EnhancedRelationListConfigParser();

        $configParser->preMap([], $contextualizer);
        $this->assertTrue(true);
    }

    public function testPostMap()
    {
        $contextualizer = $this->createMock(Contextualizer::class);
        $configParser   = new EnhancedRelationListConfigParser();

        $configParser->postMap([], $contextualizer);
        $this->assertTrue(true);
    }
}
