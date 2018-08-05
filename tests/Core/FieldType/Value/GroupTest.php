<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 10:27
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType\Value;

use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testConstruct()
    {
        $filteredValue = new Group(
            'test_group',
            ['test', 'regular_group' => new Relation(['contentId' => 112, 'attributes' => []]), null, 111, []]
        );
        $regularValue  = new Group(
            'test_group',
            [new Relation(['contentId' => 112, 'attributes' => []])]
        );

        $this->assertEquals($regularValue, $filteredValue, 'Value should remove invalid relations on construct.');
    }
}
