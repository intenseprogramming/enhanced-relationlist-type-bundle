<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 17:12
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\Core\FieldType\Value as BaseValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;

/**
 * Class Value.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Value extends BaseValue
{
    /** @var array|Relation[] $relations */
    public $relations;

    /**
     * Value constructor.
     *
     * @param array $relations
     */
    public function __construct(array $relations = [])
    {
        $this->relations = [];

        foreach ($relations as $relation) {
            if (is_array($relation) && isset($relation['contentId']) && isset($relation['attributes'])) {
                foreach ($relation['attributes'] as $attributeIndex => $value) {
                    $relation['attributes'][$attributeIndex] = new AbstractValue($value);
                }

                $relation = new Relation($relation);
            }

            if (!($relation instanceof Relation)) {
                continue;
            }

            $this->relations[] = $relation;
        }
    }

    /**
     * Returns an imploded string of content ids in relation for text representation.
     *
     * @return string
     */
    public function __toString()
    {
        return implode(
            ', ',
            array_map(
                function (Relation $relation) {
                    return $relation->contentId;
                },
                $this->relations
            )
        );
    }
}