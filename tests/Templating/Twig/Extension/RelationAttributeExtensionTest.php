<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 11:08
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Templating\Twig\Extension;

use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Templating\Twig\AttributeBlockRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\TwigFunction;

class RelationAttributeExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $connection     = $this->createMock(Connection::class);
        $blockRenderer  = $this->createMock(AttributeBlockRenderer::class);
        $contentService = $this->createMock(ContentService::class);

        $extension = new RelationAttributeExtension($blockRenderer, $connection, $contentService);

        /** @var TwigFunction[] $functions */
        $functions     = $extension->getFunctions();
        /** @var TwigFunction[] $functionNames */
        $functionNames = [];

        foreach ($functions as $function) {
            if (in_array($function->getName(), ['erl_render_attribute', 'erl_render_attribute_definition'])) {
                $this->assertTrue($function->isDeprecated());
            } else {
                $this->assertFalse($function->isDeprecated());
            }

            $functionNames[] = $function->getName();
        }

        $this->assertTrue(in_array('erl_render_attribute', $functionNames));
        $this->assertTrue(in_array('erl_render_relation_attribute', $functionNames));
        $this->assertTrue(in_array('erl_render_attribute_definition', $functionNames));
        $this->assertTrue(in_array('erl_render_relation_attribute_definition', $functionNames));
    }

    public function testRenderAttribute()
    {
        $environment    = $this->createMock(Environment::class);
        $connection     = $this->createMock(Connection::class);
        $statement      = $this->createMock(Statement::class);
        $contentService = $this->createMock(ContentService::class);
        $blockRenderer  = $this->createMock(AttributeBlockRenderer::class);

        $content           = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => 123,
                ]),
            ]),
            'contentType' => new ContentType([
                'fieldDefinitions' => new FieldDefinitionCollection([
                    new FieldDefinition([
                        'identifier'    => 'relation_field',
                        'fieldSettings' => [
                            'attributeDefinitions' => [
                                'integer' => ['the field settings'],
                            ],
                        ],
                    ]),
                ]),
            ]),
        ]);
        $attribute1        = new Integer(['value' => 123]);
        $attribute2        = new Integer(['value' => 124]);
        $unmappedAttribute = new Integer(['value' => 124]);
        $field             = new Field([
            'id'                 => 1234,
            'fieldDefIdentifier' => 'relation_field',
            'value'              => new Value(
                [
                    new Value\Relation([
                        'contentId'  => 443,
                        'attributes' => [
                            'integer' => $attribute1,
                        ],
                    ]),
                ],
                [
                    new Value\Group(
                        'group',
                        [
                            new Value\Relation([
                                'contentId'  => 445,
                                'attributes' => [
                                    'integer' => $attribute2,
                                ],
                            ]),
                        ]
                    ),
                ]
            ),
        ]);

        $blockRenderer->expects($this->at(0))->method('setTwig')->with($environment);
        $blockRenderer->expects($this->at(1))->method('renderAttributeView')->with(
            $attribute1,
            ['the field settings'],
            ['content' => $content, 'field' => $field]
        );
        $blockRenderer->expects($this->at(2))->method('setTwig')->with($environment);
        $blockRenderer->expects($this->at(3))->method('renderAttributeView')->with(
            $attribute2,
            ['the field settings'],
            ['content' => $content, 'field' => $field]
        );
        $connection->expects($this->at(0))->method('prepare')->willReturn($statement);
        $connection->expects($this->at(1))->method('prepare')->willReturn($statement);
        $connection->expects($this->at(2))->method('prepare')->willReturn($statement);
        $statement->expects($this->at(0))->method('execute');
        $statement->expects($this->at(1))->method('fetchColumn')->willReturn(123);
        $statement->expects($this->at(2))->method('execute');
        $statement->expects($this->at(3))->method('fetchColumn')->willReturn(123);
        $statement->expects($this->at(4))->method('execute');
        $statement->expects($this->at(5))->method('fetchColumn')->willReturn(123);
        $contentService->expects($this->at(0))->method('loadContent')->with(123)->willReturn($content);
        $contentService->expects($this->at(1))->method('loadContent')->with(123)->willReturn($content);
        $contentService->expects($this->at(2))->method('loadContent')->with(123)->willReturn($content);

        $extension = new RelationAttributeExtension($blockRenderer, $connection, $contentService);

        /** @var TwigFunction[] $functions */
        $functions = $extension->getFunctions();
        foreach ($functions as $function) {
            if ($function->getName() !== 'erl_render_attribute') {
                continue;
            }

            /** @var Closure $callable */
            $callable = $function->getCallable();
            $callable->call($extension, $environment, $field, $attribute1, []);
            $callable->call($extension, $environment, $field, $attribute2, []);

            $this->expectExceptionMessage(
                'Attribute O:67:"IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer":1:{s:5:"value";i:124;} does not belong to field #1234'
            );
            $callable->call($extension, $environment, $field, $unmappedAttribute, []);
            break;
        }
    }

    public function testRenderRelationAttribute()
    {
        $environment    = $this->createMock(Environment::class);
        $connection     = $this->createMock(Connection::class);
        $contentService = $this->createMock(ContentService::class);
        $blockRenderer  = $this->createMock(AttributeBlockRenderer::class);

        $content   = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'id' => 123,
                ]),
            ]),
            'contentType' => new ContentType([
                'fieldDefinitions' => new FieldDefinitionCollection([
                    new FieldDefinition([
                        'identifier'    => 'relation_field',
                        'fieldSettings' => [
                            'attributeDefinitions' => [
                                'integer' => ['the field settings'],
                            ],
                        ],
                    ]),
                ]),
            ]),
        ]);
        $attribute = new Integer(['value' => 123]);
        $relation  = new Value\Relation([
            'contentId'  => 444,
            'attributes' => [
                'integer' => $attribute,
            ],
        ]);
        $field     = new Field([
            'id'                 => 1234,
            'fieldDefIdentifier' => 'relation_field',
            'value'              => new Value([$relation]),
        ]);

        $blockRenderer->expects($this->at(0))->method('setTwig')->with($environment);
        $blockRenderer->expects($this->at(1))->method('renderAttributeView')->with(
            $attribute,
            ['the field settings'],
            ['content' => $content, 'field' => $field]
        );

        $extension = new RelationAttributeExtension($blockRenderer, $connection, $contentService);

        /** @var TwigFunction[] $functions */
        $functions = $extension->getFunctions();
        foreach ($functions as $function) {
            if ($function->getName() !== 'erl_render_relation_attribute') {
                continue;
            }

            /** @var Closure $callable */
            $callable = $function->getCallable();
            $callable->call($extension, $environment, $content, $field, $relation, 'integer', []);
            break;
        }
    }

    public function testRenderAttributeDefinition()
    {
        $environment    = $this->createMock(Environment::class);
        $connection     = $this->createMock(Connection::class);
        $contentService = $this->createMock(ContentService::class);
        $blockRenderer  = $this->createMock(AttributeBlockRenderer::class);

        $attributeDefinition = ['the field setting'];
        $fieldDefinition     = new FieldDefinition([
            'fieldSettings' => [
                'attributeDefinitions' => [
                    'integer' => $attributeDefinition,
                ],
            ],
        ]);

        $blockRenderer->expects($this->at(0))->method('setTwig')->with($environment);
        $blockRenderer->expects($this->at(1))->method('renderAttributeDefinitionView')->with(
            'integer',
            $attributeDefinition,
            [
                'fieldDefinition' => $fieldDefinition,
            ]
        );

        $extension = new RelationAttributeExtension($blockRenderer, $connection, $contentService);

        /** @var TwigFunction[] $functions */
        $functions = $extension->getFunctions();
        foreach ($functions as $function) {
            if ($function->getName() !== 'erl_render_attribute_definition') {
                continue;
            }

            /** @var Closure $callable */
            $callable = $function->getCallable();
            $callable->call($extension, $environment, $fieldDefinition, $attributeDefinition, []);
            break;
        }
    }

    public function testRenderRelationAttributeDefinition()
    {
        $environment    = $this->createMock(Environment::class);
        $connection     = $this->createMock(Connection::class);
        $contentService = $this->createMock(ContentService::class);
        $blockRenderer  = $this->createMock(AttributeBlockRenderer::class);

        $attributeDefinition = ['the field setting'];
        $fieldDefinition     = new FieldDefinition([
            'fieldSettings' => [
                'attributeDefinitions' => [
                    'integer' => $attributeDefinition,
                ],
            ],
        ]);

        $blockRenderer->expects($this->at(0))->method('setTwig')->with($environment);
        $blockRenderer->expects($this->at(1))->method('renderAttributeDefinitionView')->with(
            'integer',
            $attributeDefinition,
            [
                'fieldDefinition' => $fieldDefinition,
            ]
        );

        $extension = new RelationAttributeExtension($blockRenderer, $connection, $contentService);

        /** @var TwigFunction[] $functions */
        $functions = $extension->getFunctions();
        foreach ($functions as $function) {
            if ($function->getName() !== 'erl_render_relation_attribute_definition') {
                continue;
            }

            /** @var Closure $callable */
            $callable = $function->getCallable();
            $callable->call($extension, $environment, $fieldDefinition, 'integer', []);
            break;
        }
    }
}
