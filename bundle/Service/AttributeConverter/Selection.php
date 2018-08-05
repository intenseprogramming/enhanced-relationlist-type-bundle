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

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Selection as SelectionValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class Selection.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service\Attribute
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Selection extends RelationAttributeConverter
{
    /**
     * Generates a hash for the attribute value.
     *
     * @param RelationAttributeBase $attribute
     *
     * @return mixed
     */
    public function toHash(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof SelectionValue) {
            return $attribute->selection;
        }

        return null;
    }

    /**
     * Generates an attribute value from hash.
     *
     * @param $hash
     *
     * @return RelationAttributeBase
     */
    public function fromHash($hash)
    {
        if (!is_array($hash) && is_numeric($hash)) {
            $hash = [$hash];
        }

        return new SelectionValue(['selection' => $hash]);
    }

    /**
     * Validates the attribute value against the definition.
     *
     * @param RelationAttributeBase $attribute
     * @param array                 $definition
     *
     * @return array
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
     * @return boolean
     */
    public function isEmpty(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof SelectionValue) {
            return empty($attribute->selection);
        }

        return true;
    }

    /**
     * Returns an empty value of the type.
     *
     * @return RelationAttributeBase
     */
    public function getEmptyValue()
    {
        return new SelectionValue();
    }
}
