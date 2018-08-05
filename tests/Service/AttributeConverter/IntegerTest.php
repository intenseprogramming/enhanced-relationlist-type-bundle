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

use PHPUnit\Framework\TestCase;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer as Value;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Boolean as MismatchValue;

class IntegerTest extends TestCase
{
    public function testFromHash()
    {
        $integer = new Integer();

        $this->assertEquals(new Value(['value' => 1]), $integer->fromHash(1));
        $this->assertEquals(new Value(['value' => 0]), $integer->fromHash(0));
    }

    public function testIsEmpty()
    {
        $integer = new Integer();

        $this->assertFalse($integer->isEmpty(new Value(['value' => true])), 'Filled value.');
        $this->assertTrue($integer->isEmpty(new Value()), 'Empty value.');
        $this->assertTrue($integer->isEmpty(new MismatchValue(['value' => 1])), 'Mismatch to empty.');
    }

    public function testToHash()
    {
        $integer = new Integer();

        $this->assertEquals(1, $integer->toHash(new Value(['value' => true])));
        $this->assertEquals(0, $integer->toHash(new Value(['value' => false])));
        $this->assertEquals(null, $integer->toHash(new MismatchValue(['value' => 1])));
    }

    public function testValidate()
    {
        $integer = new Integer();

        $this->assertEmpty($integer->validate(new Value(['value' => 15]), []));
        $this->assertEmpty($integer->validate(new Value(), []));
        $this->assertNotEmpty($integer->validate(new Value(['value' => 200]), ['settings' => ['max' => 150]]));
        $this->assertNotEmpty($integer->validate(new Value(['value' => 200]), ['settings' => ['min' => 250]]));
        $this->assertNotEmpty($integer->validate(new Value(['value' => 'test']), []));
    }

    public function testGetEmptyValue()
    {
        $integer = new Integer();

        $this->assertTrue($integer->isEmpty($integer->getEmptyValue()), 'Empty value should be marked as empty.');
    }
}
