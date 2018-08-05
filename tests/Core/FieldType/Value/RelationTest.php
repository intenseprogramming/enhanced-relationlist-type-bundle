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

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer;
use PHPUnit\Framework\TestCase;

class RelationTest extends TestCase
{
    public function testConstruct()
    {
        $filteredValue = new Relation([
            'contentId'  => 123,
            'attributes' => [
                'test',
                'integer' => new Integer(['value' => 111]),
                null,
                111,
                [],
            ],
        ]);
        $regularValue  = new Relation([
            'contentId'  => 123,
            'attributes' => [
                'integer' => new Integer(['value' => 111]),
            ],
        ]);

        $this->assertEquals($regularValue, $filteredValue, 'Value should remove invalid relations on construct.');
    }
}
