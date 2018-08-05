<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 08:57
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service\AttributeConverter;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Selection as Value;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer as MismatchValue;
use PHPUnit\Framework\TestCase;

class SelectionTest extends TestCase
{
    public function testFromAbstractValue()
    {
        $this->markTestSkipped('Is deprecated!');
    }

    public function testFromHash()
    {
        $selection = new Selection();

        $this->assertEquals(new Value(['selection' => ['2']]), $selection->fromHash('2'));
        $this->assertEquals(new Value(['selection' => ['3', '4']]), $selection->fromHash(['3', '4']));
    }

    public function testIsEmpty()
    {
        $selection = new Selection();

        $this->assertFalse($selection->isEmpty(new Value(['selection' => ['1']])));
        $this->assertTrue($selection->isEmpty(new Value(['selection' => []])));
        $this->assertTrue($selection->isEmpty(new MismatchValue(['value' => 20])));
    }

    public function testToHash()
    {
        $selection = new Selection();

        $this->assertEquals(1, $selection->toHash(new Value(['selection' => true])));
        $this->assertEquals(0, $selection->toHash(new Value(['selection' => false])));
        $this->assertEquals(null, $selection->toHash(new MismatchValue(['value' => 20])));
    }

    public function testValidate()
    {
        $selection = new Selection();

        $this->assertEmpty($selection->validate(new Value(['selection' => false]), []));
    }
}
