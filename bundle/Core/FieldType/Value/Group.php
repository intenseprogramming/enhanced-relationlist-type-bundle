<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-04 00:10
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType\Value;

use eZ\Publish\API\Repository\Values\ValueObject;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;

/**
 * Class Group.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType\Value
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Group extends ValueObject
{
    /** @var string $names */
    public $name = [];

    /** @var array|Relation[] $relations */
    public $relations = [];

    public function __construct(string $name, array $relations = [])
    {
        $this->name = $name;

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

}
