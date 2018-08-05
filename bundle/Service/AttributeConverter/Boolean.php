<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-05 02:53
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service\AttributeConverter;

use eZ\Publish\Core\FieldType\ValidationError;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Boolean as BooleanValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class Boolean.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service\AttributeConverter
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Boolean extends RelationAttributeConverter
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
        if ($attribute instanceof BooleanValue) {
            return $attribute->value ? 1 : 0;
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
        return new BooleanValue(['value' => !!$hash]);
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
        return [];
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
        if ($attribute instanceof BooleanValue) {
            return !$attribute->value;
        }

        return true;
    }
}
