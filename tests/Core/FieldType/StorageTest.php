<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 08:08
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use eZ\Publish\SPI\Persistence\Content\Type as FieldType;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use IntProg\EnhancedRelationListBundle\Service\AttributeConverter;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    public function testNotUsedMethods()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn([]);

        $contentTypeHandler = $this->createMock(Handler::class);
        $transformer        = new RelationAttributeRepository([]);

        $storage = new Storage($contentTypeHandler, $transformer, $configResolver);

        $this->assertNull($storage->storeFieldData(new VersionInfo(), new Field(), []), 'Should do nothing');
        $this->assertNull($storage->deleteFieldData(new VersionInfo(), [], []), 'Should do nothing');
        $this->assertTrue($storage->hasFieldData(), 'Should do nothing and return true');
        $this->assertFalse($storage->getIndexData(new VersionInfo(), new Field(), []), 'Should do nothing and return false');
    }

    public function testGetFieldData()
    {
        $contentTypeHandler = $this->createMock(Handler::class);
        $contentTypeHandler
            ->expects($this->once())
            ->method('getFieldDefinition')
            ->with(222, FieldType::STATUS_DEFINED)
            ->willReturn(new FieldType\FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'fieldSettings' => [
                        'attributeDefinitions' => [
                            'integer'       => [
                                'type'     => 'integer',
                                'required' => false,
                                'names'    => [
                                    'eng-GB' => 'Integer',
                                ],
                                'settings' => [
                                    'min' => 0,
                                    'max' => 123,
                                ],
                            ],
                            'integer_extra' => [
                                'type'     => 'integer',
                                'required' => false,
                                'names'    => [
                                    'eng-GB' => 'Integer Extra',
                                ],
                                'settings' => [
                                    'min' => 0,
                                    'max' => 100,
                                ],
                            ],
                        ],
                        'groupSettings'        => [
                            'groups' => [
                                'group_name'  => [
                                    'eng-GB' => 'Test 123',
                                ],
                                'extra_group' => [
                                    'fre-FR' => 'sample',
                                ],
                            ],
                        ],
                    ],
                ]),
            ]));

        $transformer = new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);

        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type    = new Type($transformer);
        $storage = new Storage($contentTypeHandler, $transformer, $configResolver);

        $field = new Field([
            'fieldDefinitionId' => 222,
            'value'             => $type->toPersistenceValue($this->getSampleValue()),
        ]);
        $storage->getFieldData(new VersionInfo(), $field, ['eng-GB']);

        $this->assertEquals(
            [
                'relations' => [
                    [
                        'contentId'  => 123,
                        'attributes' => [
                            'integer'   => [
                                'value' => 123,
                                'type'  => 'integer',
                            ],
                            'integer_extra' => [
                                'value' => null,
                                'type'  => 'integer',
                            ],
                        ],
                    ],
                    [
                        'contentId'  => 312,
                        'attributes' => [
                            'integer'   => [
                                'value' => 13,
                                'type'  => 'integer',
                            ],
                            'integer_extra' => [
                                'value' => null,
                                'type'  => 'integer',
                            ],
                        ],
                    ],
                ],
                'groups'    => [
                    'group_name'  => [
                        'name'      => 'Test 123',
                        'relations' => [
                            [
                                'contentId'  => 987,
                                'attributes' => [
                                    'integer'       => [
                                        'value' => 22,
                                        'type'  => 'integer',
                                    ],
                                    'integer_extra' => [
                                        'value' => null,
                                        'type'  => 'integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'extra_group' => [
                        'name'      => 'sample',
                        'relations' => [],
                    ],
                    'custom_group'  => [
                        'name'      => 'custom_group',
                        'relations' => [
                            [
                                'contentId'  => 987,
                                'attributes' => [
                                    'integer'       => [
                                        'value' => 22,
                                        'type'  => 'integer',
                                    ],
                                    'integer_extra' => [
                                        'value' => null,
                                        'type'  => 'integer',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            $field->value->data
        );
    }

    protected function getSampleValue()
    {
        return new Value(
            [
                new Relation([
                    'contentId'  => 123,
                    'attributes' => [
                        'boolean'   => new Attribute\Boolean(['value' => true]),
                        'integer'   => new Attribute\Integer(['value' => '123']),
                        'selection' => new Attribute\Selection(['selection' => ['1']]),
                        'string'    => new Attribute\TextLine(['value' => 'test string 1']),
                    ],
                ]),
                new Relation([
                    'contentId'  => 312,
                    'attributes' => [
                        'boolean'   => new Attribute\Boolean(['value' => false]),
                        'integer'   => new Attribute\Integer(['value' => '13']),
                        'selection' => new Attribute\Selection(['selection' => ['2']]),
                        'string'    => new Attribute\TextLine(['value' => 'test string 2']),
                    ],
                ]),
            ],
            [
                new Group(
                    'group_name',
                    [
                        new Relation([
                            'contentId'  => 987,
                            'attributes' => [
                                'boolean'   => new Attribute\Boolean(['value' => false]),
                                'integer'   => new Attribute\Integer(['value' => '22']),
                                'selection' => new Attribute\Selection(['selection' => ['3']]),
                                'string'    => new Attribute\TextLine(['value' => 'test string 3']),
                            ],
                        ]),
                    ]
                ),
                new Group(
                    'custom_group',
                    [
                        new Relation([
                            'contentId'  => 987,
                            'attributes' => [
                                'boolean'   => new Attribute\Boolean(['value' => false]),
                                'integer'   => new Attribute\Integer(['value' => '22']),
                                'selection' => new Attribute\Selection(['selection' => ['3']]),
                                'string'    => new Attribute\TextLine(['value' => 'test string 3']),
                            ],
                        ]),
                    ]
                ),
            ]
        );
    }
}
