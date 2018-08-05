<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 10:21
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    public function testConstruct()
    {
        $filteredValue = new Value(
            [[], new Relation(['contentId' => 123, 'attributes' => []]), 123, []],
            ['test', 'regular_group' => new Group('regular_group'), null, 111, []]
        );
        $regularValue  = new Value(
            [new Relation(['contentId' => 123, 'attributes' => []])],
            ['regular_group' => new Group('regular_group')]
        );

        $this->assertEquals($regularValue, $filteredValue, 'Value should remove invalid relations on construct.');
    }
}
