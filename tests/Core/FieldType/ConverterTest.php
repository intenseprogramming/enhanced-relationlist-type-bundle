<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 03:54
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldTypeConstraints;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Service\AttributeConverter;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function testGetIndexColumn()
    {
        $converter = new Converter();

        $this->assertEquals('sort_key_string', $converter->getIndexColumn());
    }

    public function testValueConversion()
    {
        $converter   = new Converter();
        $transformer = new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);

        $testValue       = new FieldValue();
        $testValue->data = (new Type($transformer))->toHash($this->getTestValue());

        $storageValue = new StorageFieldValue();
        $fieldValue   = new FieldValue();

        $converter->toStorageValue($testValue, $storageValue);
        $converter->toFieldValue($storageValue, $fieldValue);

        $this->assertEquals($testValue, $fieldValue, 'Store and retrieve should result in same field value.');
    }

    public function testEmptyFieldValueConversion()
    {
        $converter  = new Converter();
        $emptyValue = new FieldValue();

        $storageValue = new StorageFieldValue();
        $fieldValue   = new FieldValue();

        $converter->toFieldValue($storageValue, $fieldValue);

        $this->assertEquals($emptyValue, $fieldValue, 'Should abort conversion if not data text is set.');
    }

    public function testFieldDefinitionConversion()
    {
        $converter   = new Converter();
        $transformer = new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);
        $type        = new Type($transformer);

        $fieldTypeConstraints                 = new FieldTypeConstraints([
            'validators'    => $type->validatorConfigurationToHash($this->getTestDefinitionValidation()),
            'fieldSettings' => $type->fieldSettingsToHash($this->getTestDefinitionSettings()),
        ]);
        $testDefinition                       = new FieldDefinition();
        $testDefinition->fieldTypeConstraints = $fieldTypeConstraints;

        $storageFieldDefinition = new StorageFieldDefinition();
        $fieldDefinition        = new FieldDefinition();

        $converter->toStorageFieldDefinition($testDefinition, $storageFieldDefinition);
        $converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);

        $this->assertEquals($testDefinition, $fieldDefinition, 'Store and retrieve should result in same field definition.');
    }

    public function testFieldDefinitionAbortOnEmptyConstraints()
    {
        $converter   = new Converter();
        $transformer = new RelationAttributeRepository([]);
        $type        = new Type($transformer);

        $storageFieldDefinition = new StorageFieldDefinition();
        $fieldDefinition        = new FieldDefinition();

        $defaultSettings = [];
        $defaultValidator = [];
        $type->applyDefaultSettings($defaultSettings);
        $type->applyDefaultValidatorConfiguration($defaultValidator);

        $toFieldDefinition = new FieldDefinition([
            'fieldTypeConstraints' => new FieldTypeConstraints([
                'validators'    => $type->validatorConfigurationToHash($defaultValidator),
                'fieldSettings' => $type->fieldSettingsToHash($defaultSettings),
            ]),
        ]);

        $converter->toStorageFieldDefinition($fieldDefinition, $storageFieldDefinition);
        $converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);

        $this->assertEquals(new StorageFieldDefinition(), $storageFieldDefinition, 'Should abort to storage definition on empty constraints.');
        $this->assertEquals($toFieldDefinition, $fieldDefinition, 'Should set default definition on empty constraints.');
    }

    protected function getTestValue()
    {
        return new Value(
            [
                new Value\Relation(
                    [
                        'contentId'  => 1,
                        'attributes' => [
                            'boolean'   => new Attribute\Boolean(['value' => true]),
                            'integer'   => new Attribute\Integer(['value' => '123']),
                            'selection' => new Attribute\Selection(['selection' => ['2']]),
                            'string'    => new Attribute\TextLine(['value' => 'test string']),
                        ],
                    ]
                ),
            ],
            [
                new Value\Group(
                    'test_group',
                    [
                        new Value\Relation(
                            [
                                'contentId'  => 33,
                                'attributes' => [
                                    'boolean'   => new Attribute\Boolean(['value' => false]),
                                    'integer'   => new Attribute\Integer(['value' => '321']),
                                    'selection' => new Attribute\Selection(['selection' => ['1']]),
                                    'string'    => new Attribute\TextLine(['value' => 'another test string']),
                                ],
                            ]
                        ),
                    ]
                ),
            ]
        );
    }

    protected function getTestDefinitionSettings()
    {
        return [
            'attributeDefinitions'     => [
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
                'test_required' => [
                    'type'     => 'integer',
                    'required' => true,
                    'names'    => [
                        'eng-GB' => 'Integer',
                    ],
                    'settings' => [],
                ],
            ],
            'defaultBrowseLocation'    => 22,
            'selectionLimit'           => 20,
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
        ];
    }

    protected function getTestDefinitionValidation()
    {
        return [
            'relationValidator' => [
                'allowedContentTypes' => [
                    'test_type_1',
                    'test_type_2',
                ],
            ],
        ];
    }
}
