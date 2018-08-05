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
            if (!($relation instanceof Relation)) {
                continue;
            }

            $this->relations[] = $relation;
        }
    }
}
