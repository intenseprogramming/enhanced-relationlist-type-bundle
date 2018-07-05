<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-05 02:51
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute;

use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;

/**
 * Class Boolean.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Boolean extends RelationAttributeBase
{
    /** @var integer $value */
    public $value;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        $this->value = !!$this->value;
    }

    /**
     * Returns the type identifier of the relation attribute.
     *
     * @return string
     */
    public function getTypeIdentifier()
    {
        return 'boolean';
    }
}
