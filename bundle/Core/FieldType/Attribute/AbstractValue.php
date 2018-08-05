<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-05-24 22:12
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute;

use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;

/**
 * Class AbstractValue.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 *
 * @deprecated will be removed in first stable release.
 */
class AbstractValue extends RelationAttributeBase
{
    /** @var mixed $value */
    public $value;

    /**
     * AbstractValue constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        parent::__construct(['value' => $value]);
    }

    /**
     * Returns the type identifier of the relation attribute.
     *
     * @return string
     */
    public function getTypeIdentifier()
    {
        return '_abstract';
    }
}
