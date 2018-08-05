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

use eZ\Publish\SPI\FieldType\ValidationError;

/**
 * Class RelationAttributeConverter.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
abstract class RelationAttributeConverter
{
    /**
     * Generates a hash for the attribute value.
     *
     * @param RelationAttributeBase $attribute
     *
     * @return mixed
     */
    abstract public function toHash(RelationAttributeBase $attribute);

    /**
     * Generates an attribute value from hash.
     *
     * @param $hash
     *
     * @return RelationAttributeBase
     */
    abstract public function fromHash($hash);

    /**
     * Validates the attribute value against the definition.
     *
     * @param RelationAttributeBase $attribute
     * @param array                 $definition
     *
     * @return array|ValidationError[]
     */
    abstract public function validate(RelationAttributeBase $attribute, $definition);

    /**
     * Returns true if the value is empty.
     *
     * @param RelationAttributeBase $attribute
     *
     * @return boolean
     */
    abstract public function isEmpty(RelationAttributeBase $attribute);

    /**
     * Returns an empty value of the type.
     *
     * @return RelationAttributeBase
     */
    abstract public function getEmptyValue();
}
