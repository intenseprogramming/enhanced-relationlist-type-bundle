<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-21 03:25
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service\AttributeConverter;

use eZ\Publish\Core\FieldType\ValidationError;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer as IntegerValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class Integer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service\Attribute
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Integer extends RelationAttributeConverter
{
    /**
     * Generates a value from abstract value.
     *
     * @param AbstractValue $abstractValue
     *
     * @return mixed
     *
     * @deprecated will be removed in first stable release.
     */
    public function fromAbstractValue(AbstractValue $abstractValue)
    {
        return $this->fromHash($abstractValue->value);
    }

    /**
     * Generates a hash for the attribute value.
     *
     * @param RelationAttributeBase $attribute
     *
     * @return mixed
     */
    public function toHash(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof IntegerValue) {
            return $attribute->value;
        }

        return null;
    }

    /**
     * Generates an attribute value from hash.
     *
     * @param $hash
     *
     * @return mixed
     */
    public function fromHash($hash)
    {
        return new IntegerValue(['value' => $hash]);
    }

    /**
     * Validates the attribute value against the definition.
     *
     * @param RelationAttributeBase $attribute
     * @param array                 $definition
     *
     * @return mixed
     */
    public function validate(RelationAttributeBase $attribute, $definition)
    {
        $errors = [];

        if ($attribute instanceof IntegerValue) {
            if ($this->isEmpty($attribute)) {
                return [];
            }

            if (!is_numeric($attribute->value)) {
                $errors[] = new ValidationError('Value required to be numeric.');
            } else {
                $min = $definition['settings']['min'] ?? null;
                $max = $definition['settings']['max'] ?? null;

                if ($min !== null && $min > $attribute->value) {
                    $errors[] = new ValidationError('Value required to be above %min%.', null, ['%min%' => $min]);
                } elseif ($max !== null && $max < $attribute->value) {
                    $errors[] = new ValidationError('Value required to be below %max%.', null, ['%max%' => $max]);
                }
            }
        }

        return $errors;
    }

    /**
     * Returns true if the value is empty.
     *
     * @param RelationAttributeBase $attribute
     *
     * @return mixed
     */
    public function isEmpty(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof IntegerValue) {
            return $attribute->value === null || $attribute->value === '';
        }

        return true;
    }
}
