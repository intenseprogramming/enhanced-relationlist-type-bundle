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
    public function fromAbstractValue(AbstractValue $abstractValue)
    {
        return $this->fromHash($abstractValue->value);
    }

    public function toHash(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof BooleanValue) {
            return $attribute->value ? 1 : 0;
        }

        return null;
    }

    public function fromHash($hash)
    {
        return new BooleanValue(['value' => !!$hash]);
    }

    public function validate(RelationAttributeBase $attribute, $definition)
    {
        return [];
    }

    public function isEmpty(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof BooleanValue) {
            return !$attribute->value;
        }

        return true;
    }
}
