<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 17:06
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\API\Repository\Values\Content\Relation as ApiRelation;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Nameable;
use eZ\Publish\SPI\FieldType\Value as SpiValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue as PersistenceValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;

/**
 * Class Type.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Type extends FieldType implements Nameable
{
    /** @var RelationAttributeRepository $relationAttributeTransformer */
    protected $relationAttributeTransformer;

    protected $settingsSchema = [
        'attributeDefinitions'  => [
            'type'    => 'array',
            'default' => [],
        ],
        'defaultBrowseLocation' => [
            'type'    => 'int',
            'default' => 0,
        ],
        'selectionLimit'        => [
            'type'    => 'int',
            'default' => 0,
        ],
        'groupSettings'         => [
            'positionsFixed' => [
                'type'    => 'bool',
                'default' => false,
            ],
            'extendable'     => [
                'type'    => 'bool',
                'default' => true,
            ],
            'allowUngrouped' => [
                'type'    => 'bool',
                'default' => true,
            ],
            'groups'         => [
                'type'    => 'array',
                'default' => [],
            ],
        ]
    ];

    protected $validatorConfigurationSchema = [
        'relationValidator' => [
            'allowedContentTypes' => [
                'type'    => 'array',
                'default' => [],
            ],
        ],
    ];

    /**
     * Applies the default values to the fieldSettings of a FieldDefinitionCreateStruct.
     *
     * @param mixed $fieldSettings
     */
    public function applyDefaultSettings(&$fieldSettings)
    {
        foreach ($this->getSettingsSchema() as $key => $value) {
            if (!isset($value['default'])) {
                foreach ($value as $subKey => $subValue) {
                    $fieldSettings[$key][$subKey] = $subValue['default'];
                }

                continue;
            }

            $fieldSettings[$key] = $value['default'];
        }

        return;
    }

    /**
     * Type constructor.
     *
     * @param RelationAttributeRepository $relationAttributeTransformer
     */
    public function __construct(RelationAttributeRepository $relationAttributeTransformer)
    {
        $this->relationAttributeTransformer = $relationAttributeTransformer;
    }

    /**
     * Returns the field type identifier for this field type.
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'intprogenhancedrelationlist';
    }

    /**
     * Returns a human readable string representation from the given $value.
     *
     * @param SpiValue $value
     *
     * @return string
     */
    public function getName(SpiValue $value)
    {
        return (string) $value;
    }

    /**
     * Returns the empty value for this field type.
     *
     * @return Value
     */
    public function getEmptyValue()
    {
        return new Value();
    }

    /**
     * Converts an $hash to the Value defined by the field type.
     *
     * @param mixed $hash
     *
     * @return SpiValue
     */
    public function fromHash($hash)
    {
        if (is_string($hash)) {
            $hash = json_decode($hash, true);
        }

        if (is_array($hash['relations'] ?? false) || is_array($hash['groups'] ?? false)) {
            $relations = [];
            if (is_array($hash['relations'] ?? false)) {
                $relations = $hash['relations'];
            }

            $groups = [];
            if (is_array($hash['groups'] ?? false)) {
                $groups = $hash['groups'];
            }

            return new Value($relations, $groups);
        }

        return $this->getEmptyValue();
    }

    /**
     * Converts the given $value into a plain hash format.
     *
     * @param SpiValue|Value $value
     *
     * @return array
     */
    public function toHash(SpiValue $value)
    {
        $hash = [
            'relations' => [],
            'groups'    => [],
        ];

        foreach ($value->relations as $relation) {
            $subHash = [
                'contentId'  => $relation->contentId,
                'attributes' => [],
            ];

            foreach ($relation->attributes as $identifier => $attribute) {
                $subHash['attributes'][$identifier] = [
                    'value' => $this->relationAttributeTransformer->toPersistentValue($attribute),
                    'type'  => $attribute->getTypeIdentifier(),
                ];
            }

            $hash['relations'][] = $subHash;
        }

        foreach ($value->groups ?? [] as $groupName => $group) {
            $hash['groups'][$groupName] = [];

            foreach ($group->relations as $relation) {
                $subHash = [
                    'contentId'  => $relation->contentId,
                    'attributes' => [],
                ];

                foreach ($relation->attributes as $identifier => $attribute) {
                    $subHash['attributes'][$identifier] = [
                        'value' => $this->relationAttributeTransformer->toPersistentValue($attribute),
                        'type'  => $attribute->getTypeIdentifier(),
                    ];
                }

                $hash['groups'][$groupName][] = $subHash;
            }
        }

        return $hash;
    }

    /**
     * Converts a persistence $fieldValue to a Value.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     *
     * @return Value
     */
    public function fromPersistenceValue(PersistenceValue $fieldValue)
    {
        if (is_array($fieldValue->data) && !empty($fieldValue->data)) {
            $relations = [];
            $groups    = [];

            if (is_array($fieldValue->data['relations'] ?? false) && !empty($fieldValue->data['relations'])) {
                foreach ($fieldValue->data['relations'] as $datum) {
                    $relation = [
                        'contentId'  => $datum['contentId'],
                        'attributes' => [],
                    ];

                    foreach ($datum['attributes'] as $identifier => $attribute) {
                        $relation['attributes'][$identifier] =
                            $this->relationAttributeTransformer->fromPersistentValue(
                                $attribute['value'],
                                $attribute['type']
                            );
                    }

                    $relations[] = new Relation($relation);
                }
            }

            if (is_array($fieldValue->data['groups'] ?? false) && !empty($fieldValue->data['groups'])) {
                foreach ($fieldValue->data['groups'] as $groupName => $group) {
                    $groupRelations = [];

                    foreach ($group as $datum) {
                        $relation = [
                            'contentId'  => $datum['contentId'],
                            'attributes' => [],
                        ];

                        foreach ($datum['attributes'] as $identifier => $attribute) {
                            $relation['attributes'][$identifier] =
                                $this->relationAttributeTransformer->fromPersistentValue(
                                    $attribute['value'],
                                    $attribute['type']
                                );
                        }

                        $groupRelations[] = new Relation($relation);
                    }

                    $groups[$groupName] = new Group($groupRelations);
                }
            }

            return new Value($relations, $groups);
        }

        return $this->getEmptyValue();
    }

    /**
     * Returns a human readable string representation from a given field.
     *
     * @param SpiValue        $value
     * @param FieldDefinition $fieldDefinition
     * @param string          $languageCode
     *
     * @return string
     */
    public function getFieldName(SpiValue $value, FieldDefinition $fieldDefinition, $languageCode)
    {
        return (string) $value;
    }

    /**
     * Validates the validatorConfiguration of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration($validatorConfiguration)
    {
        $validationErrors = [];

        foreach ((array) $validatorConfiguration as $validatorIdentifier => $constraints) {
            if (!isset($this->validatorConfigurationSchema[$validatorIdentifier])) {
                $validationErrors[] = new ValidationError(
                    "Validator '%validator%' is unknown",
                    null,
                    [
                        'validator' => $validatorIdentifier,
                    ],
                    "[$validatorIdentifier]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * Validates the fieldSettings of a FieldDefinitionCreateStruct or FieldDefinitionUpdateStruct.
     *
     * @param mixed $fieldSettings
     *
     * @return array
     */
    public function validateFieldSettings($fieldSettings)
    {
        $validationErrors = [];

        foreach ((array) $fieldSettings as $settingsIdentifier => $setting) {
            if ($settingsIdentifier == 'groupSettings') {
                foreach ($setting as $groupSettingIdentifier => $groupSetting) {
                    if (!isset($this->settingsSchema['groupSettings'][$groupSettingIdentifier])) {
                        $validationErrors[] = new ValidationError(
                            "Setting '%setting%' in group settings is unknown",
                            null,
                            [
                                'setting' => $groupSettingIdentifier,
                            ],
                            "[$groupSettingIdentifier]"
                        );
                    }
                }

                continue;
            }

            if (!isset($this->settingsSchema[$settingsIdentifier])) {
                $validationErrors[] = new ValidationError(
                    "Setting '%setting%' is unknown",
                    null,
                    [
                        'setting' => $settingsIdentifier,
                    ],
                    "[$settingsIdentifier]"
                );
            }
        }

        return $validationErrors;
    }

    /**
     * Validates a field based on the validators in the field definition.
     *
     * @TODO: check if the value can be transformed knowing the field definition in a more suitable location.
     *
     * @param FieldDefinition $fieldDefinition The field definition of the field
     * @param SPIValue|Value  $value           The field value for which an action is performed
     *
     * @return ValidationError[]
     */
    public function validate(FieldDefinition $fieldDefinition, SPIValue $value)
    {
        $validationErrors = [];

        $attributeDefinitions = $fieldDefinition->getFieldSettings()['attributeDefinitions'];

        foreach ($value->relations as $relation) {
            foreach ($relation->attributes as $key => $attribute) {
                if ($attribute instanceof AbstractValue) {
                    $relation->attributes[$key] =
                        $this->relationAttributeTransformer->convertAbstractValue(
                            $attribute,
                            $attributeDefinitions[$key]['type']
                        );
                }
            }
        }

        // TODO: Check if content types of relations are allowed. Would be nice.

        $relationIndex   = 0;
        $attributeErrors = [];

        if (!$fieldDefinition->getFieldSettings()['groupSettings']['allowUngrouped'] && !empty($value->relations)) {
            $validationErrors[] = new ValidationError('Ungrouped relations are not allowed.');
        }

        foreach ($value->relations as $relation) {
            foreach ($attributeDefinitions as $identifier => $attributeDefinition) {
                $errors = $this->relationAttributeTransformer->validate(
                    $relation->attributes[$identifier],
                    $identifier,
                    $attributeDefinition
                );

                if (!empty($errors)) {
                    $attributeErrors[$relationIndex][$identifier] = $errors;
                }
            }

            $relationIndex++;
        }

        foreach ($value->groups as $group) {
            foreach ($group->relations as $relation) {
                foreach ($attributeDefinitions as $identifier => $attributeDefinition) {
                    $errors = $this->relationAttributeTransformer->validate(
                        $relation->attributes[$identifier],
                        $identifier,
                        $attributeDefinition
                    );

                    if (!empty($errors)) {
                        $attributeErrors[$relationIndex][$identifier] = $errors;
                    }
                }

                $relationIndex++;
            }
        }

        $selectionLimit = $fieldDefinition->getFieldSettings()['selectionLimit'];
        if ($selectionLimit && $selectionLimit < $relationIndex) {
            new ValidationError(
                'Only %allowed% relation(s) allowed. %given% given.',
                null,
                [
                    '%allowed%' => $selectionLimit,
                    '%given%'   => count($value->relations),
                ]
            );
        }

        if (!empty($attributeErrors)) {
            $validationErrors[]     = new ValidationError('Attributes did not validate.');
            $value->attributeErrors = $attributeErrors;
        }

        return $validationErrors;
    }

    /**
     * Returns relation data extracted from value.
     *
     * Not intended for \eZ\Publish\API\Repository\Values\Content\Relation::COMMON type relations,
     * there is an API for handling those.
     *
     * @param SPIValue|Value $fieldValue
     *
     * @return array Hash with relation type as key and array of destination content ids as value.
     */
    public function getRelations(SPIValue $fieldValue)
    {
        $relations = [];

        $relations[ApiRelation::FIELD] = [];

        foreach ($fieldValue->relations as $relation) {
            $relations[ApiRelation::FIELD][] = $relation->contentId;
        }

        foreach ($fieldValue->groups as $group) {
            foreach ($group->relations as $relation) {
                $relations[ApiRelation::FIELD][] = $relation->contentId;
            }
        }

        $relations[ApiRelation::FIELD] =
            array_values(array_unique($relations[ApiRelation::FIELD]));

        return array_filter($relations);
    }

    /**
     * Returns information for FieldValue->$sortKey relevant to the field type.
     *
     * @param CoreValue $value
     *
     * @return string
     */
    protected function getSortInfo(CoreValue $value)
    {
        return 'sort_key_string';
    }

    /**
     * Inspects given $inputValue and potentially converts it into a dedicated value object.
     *
     * @param mixed $inputValue
     *
     * @return mixed
     */
    protected function createValueFromInput($inputValue)
    {
        if ($inputValue instanceof Value) {
            return $inputValue;
        }

        return $this->fromHash($inputValue);
    }

    /**
     * Throws an exception if value structure is not of expected format.
     *
     * @param CoreValue $value
     *
     * @return mixed
     */
    protected function checkValueStructure(CoreValue $value)
    {
        return $value;
    }
}
