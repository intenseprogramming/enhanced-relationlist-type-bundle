<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 17:14
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType\Value;

use eZ\Publish\API\Repository\Values\ValueObject;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;

/**
 * Class Relation.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType\Value
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Relation extends ValueObject
{
    /** @var integer $contentId */
    public $contentId;

    /** @var array|RelationAttributeBase[] */
    public $attributes;

    /**
     * Relation constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        $attributes = [];

        foreach ($this->attributes as $identifier => $attribute) {
            if (!($attribute instanceof RelationAttributeBase)) {
                continue;
            }

            $attributes[$identifier] = $attribute;
        }

        $this->attributes = $attributes;
    }
}
