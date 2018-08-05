<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 06:59
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\API\Repository\Values\Content\Relation as ApiRelation;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use IntProg\EnhancedRelationListBundle\Service\AttributeConverter;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testGetEmptyValue()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals(new Value(), $type->getEmptyValue(), 'Empty value should be an empty value object.');
    }

    public function testIsSearchable()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals(true, $type->isSearchable(), 'Type should be searchable.');
    }

    public function testGetName()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals('123, 312, 987', $type->getName($this->getSampleValue()), 'Name should be correctly generated.');
    }

    public function testFromHash()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $sampleValue = $this->getSampleValue();
        $resultValue = $type->fromHash($type->toHash($sampleValue));

        $this->assertEquals($sampleValue, $resultValue, 'Value conversion should result in unchanged structure.');
        $this->assertEquals(new Value(), $type->fromHash([]), 'Empty hash should generate empty value.');
    }

    public function testGetRelations()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals(
            [ApiRelation::FIELD => [123, 312, 987]],
            $type->getRelations($this->getSampleValue()),
            'Relations must be generated.'
        );
    }

    public function testValidate()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $failingWithDefinition = new FieldDefinition([
            'names'                  => ['eng-GB' => 'test name match'],
            'isRequired'             => true,
            'fieldSettings'          => [
                'attributeDefinitions'     => [
                    'integer' => [
                        'type'     => 'integer',
                        'required' => false,
                        'names'    => [
                            'eng-GB' => 'Integer',
                        ],
                        'settings' => [
                            'min' => 0,
                            'max' => 10,
                        ],
                    ],
                ],
                'defaultBrowseLocation'    => 22,
                'selectionLimit'           => 2,
                'selectionAllowDuplicates' => true,
                'groupSettings'            => [
                    'positionsFixed' => true,
                    'extendable'     => false,
                    'allowUngrouped' => false,
                    'groups'         => [
                        'system_group' => [
                            'eng-GB' => 'System Group',
                        ],
                    ],
                ],
            ],
            'validatorConfiguration' => [
                'relationValidator' => [
                    'allowedContentTypes' => [
                        'test_type_1',
                        'test_type_2',
                    ],
                ],
            ],
        ]);

        $this->assertEmpty($type->validate($this->getSampleDefinition(), $this->getSampleValue()));
        $this->assertNotEmpty($type->validate($failingWithDefinition, $this->getSampleValue()));
    }

    public function testGetFieldName()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals(
            '123, 312, 987',
            $type->getFieldName($this->getSampleValue(), new FieldDefinition(), 'eng-GB'),
            'Name should be correctly generated.'
        );
    }

    public function testValidateValidatorConfiguration()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $successResult = $type->validateValidatorConfiguration([
            'relationValidator' => [
                'allowedContentTypes' => [
                    'test_type_1',
                    'test_type_2',
                ],
            ],
        ]);
        $errorResult   = $type->validateValidatorConfiguration([
            'relationValidator' => [
                'allowedContentTypes' => [
                    'test_type_1',
                    'test_type_2',
                ],
                'invalidConstraint'   => 1,
            ],
            'invalidValidator'  => 2,
        ]);

        $this->assertEmpty($successResult, 'Should not result in errors');
        $this->assertNotEmpty($errorResult, 'Should result in errors');
    }

    public function testGetFieldTypeIdentifier()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals('intprogenhancedrelationlist', $type->getFieldTypeIdentifier(), 'Type identifier should match.');
    }

    public function testValidateFieldSettings()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $validSettings   = [];
        $invalidSettings = [
            'invalidSetting' => true,
            'groupSettings'  => [
                'invalidGroupSetting' => true,
            ],
        ];

        $type->applyDefaultSettings($validSettings);
        $type->applyDefaultSettings($invalidSettings);

        $this->assertEmpty($type->validateFieldSettings($validSettings), 'Settings should validate.');
        $this->assertNotEmpty($type->validateFieldSettings($invalidSettings), 'Settings should not validate.');
    }

    public function testCreateValueFromInput()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $hash = $type->toHash($this->getSampleValue());

        $this->assertEquals($this->getSampleValue(), $type->acceptValue($hash), 'Should convert hash to value.');
        $this->assertEquals($this->getSampleValue(), $type->acceptValue($this->getSampleValue()), 'Should directly accept value.');
    }

    public function testGetSortInfo()
    {
        $transformer = $this->getTransformer();
        $type        = new Type($transformer);

        $this->assertEquals('sort_key_string', $type->toPersistenceValue($this->getSampleValue())->sortKey, 'Should set sort key string as sort info.');
    }

    protected function getTransformer()
    {
        return new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);
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
            ]
        );
    }

    protected function getSampleDefinition()
    {
        return new FieldDefinition([
            'names'                  => ['eng-GB' => 'test name match'],
            'isRequired'             => true,
            'fieldSettings'          => [
                'attributeDefinitions'     => [
                    'integer' => [
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
                ],
                'defaultBrowseLocation'    => 22,
                'selectionLimit'           => 20,
                'selectionAllowDuplicates' => true,
                'groupSettings'            => [
                    'positionsFixed' => true,
                    'extendable'     => false,
                    'allowUngrouped' => true,
                    'groups'         => [
                        'system_group' => [
                            'eng-GB' => 'System Group',
                        ],
                    ],
                ],
            ],
            'validatorConfiguration' => [
                'relationValidator' => [
                    'allowedContentTypes' => [
                        'test_type_1',
                        'test_type_2',
                    ],
                ],
            ],
        ]);
    }
}
