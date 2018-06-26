<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-21 03:29
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;

/**
 * Class RelationAttributeConverter.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
abstract class RelationAttributeConverter
{
    abstract public function fromAbstractValue(AbstractValue $abstractValue);

    abstract public function toHash(RelationAttributeBase $attribute);

    abstract public function fromHash($hash);

    abstract public function validate(RelationAttributeBase $attribute, $definition);

    abstract public function isEmpty(RelationAttributeBase $attribute);
}
