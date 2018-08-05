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
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
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

    /** @var array|Group[] $groups */
    public $groups;

    /** @var array $attributeErrors Only used on edit to inject validation errors */
    public $attributeErrors = [];

    /**
     * Value constructor.
     *
     * @param array|Relation[] $relations
     * @param array|Group[]    $groups
     */
    public function __construct(array $relations = [], array $groups = [])
    {
        $this->relations = [];
        $this->groups    = [];

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

        foreach ($groups as $groupName => $group) {
            if (!($group instanceof Group)) {
                $group = new Group($groupName, $group);
            }

            $this->groups[$group->name] = $group;
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
