<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-11 20:26
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class RelationAttributeTransformer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class RelationAttributeTransformer
{
    /** @var array|RelationAttributeConverter[] $converters */
    protected $converters = [];

    /**
     * RelationAttributeTransformer constructor.
     *
     * @param array|RelationAttributeConverter[] $converters
     */
    public function __construct(array $converters)
    {
        $this->converters = $converters;
    }

    public function convertAbstractValue(AbstractValue $abstractValue, $targetType)
    {
        return $this->converters[$targetType]->fromAbstractValue($abstractValue);
    }

    public function fromPersistentValue($value, $targetType)
    {
        return $this->converters[$targetType]->fromHash($value);
    }

    public function toPersistentValue(RelationAttributeBase $attribute)
    {
        return $this->converters[$attribute->getTypeIdentifier()]->toHash($attribute);
    }
}
