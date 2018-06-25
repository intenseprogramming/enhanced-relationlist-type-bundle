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
    public function fromAbstractValue(AbstractValue $abstractValue)
    {
        return $this->fromHash($abstractValue->value);
    }

    public function toHash(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof IntegerValue) {
            return $attribute->value;
        }

        return null;
    }

    public function fromHash($hash)
    {
        return new IntegerValue(['value' => $hash]);
    }
}
