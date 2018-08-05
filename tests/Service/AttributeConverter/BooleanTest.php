<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 08:56
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service\AttributeConverter;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Boolean as Value;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer as MismatchValue;
use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function testFromAbstractValue()
    {
        $this->markTestSkipped('Is deprecated!');
    }

    public function testFromHash()
    {
        $boolean = new Boolean();

        $this->assertEquals(new Value(['value' => true]), $boolean->fromHash(1));
        $this->assertEquals(new Value(['value' => false]), $boolean->fromHash(0));
    }

    public function testIsEmpty()
    {
        $boolean = new Boolean();

        $this->assertFalse($boolean->isEmpty(new Value(['value' => true])));
        $this->assertTrue($boolean->isEmpty(new Value(['value' => false])));
        $this->assertTrue($boolean->isEmpty(new MismatchValue(['value' => 20])));
    }

    public function testToHash()
    {
        $boolean = new Boolean();

        $this->assertEquals(1, $boolean->toHash(new Value(['value' => true])));
        $this->assertEquals(0, $boolean->toHash(new Value(['value' => false])));
        $this->assertEquals(null, $boolean->toHash(new MismatchValue(['value' => 20])));
    }

    public function testValidate()
    {
        $boolean = new Boolean();

        $this->assertEmpty($boolean->validate(new Value(['value' => false]), []));
    }
}
