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

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\TextLine as Value;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer as MismatchValue;
use PHPUnit\Framework\TestCase;

class TextLineTest extends TestCase
{
    public function testFromHash()
    {
        $textLine = new TextLine();

        $this->assertEquals(new Value(['value' => true]), $textLine->fromHash(1));
        $this->assertEquals(new Value(['value' => false]), $textLine->fromHash(0));
    }

    public function testIsEmpty()
    {
        $textLine = new TextLine();

        $this->assertFalse($textLine->isEmpty(new Value(['value' => 'test 111'])));
        $this->assertTrue($textLine->isEmpty(new Value(['value' => ''])));
        $this->assertTrue($textLine->isEmpty(new MismatchValue(['value' => 20])));
    }

    public function testToHash()
    {
        $textLine = new TextLine();

        $this->assertEquals(1, $textLine->toHash(new Value(['value' => true])));
        $this->assertEquals(0, $textLine->toHash(new Value(['value' => false])));
        $this->assertEquals(null, $textLine->toHash(new MismatchValue(['value' => 20])));
    }

    public function testValidate()
    {
        $textLine = new TextLine();

        $this->assertEmpty($textLine->validate(new Value(['value' => 'test string']), []));
        $this->assertNotEmpty($textLine->validate(new Value(['value' => 'test string']), ['settings' => ['minLength' => 50]]));
        $this->assertNotEmpty($textLine->validate(new Value(['value' => 'test string']), ['settings' => ['maxLength' => 5]]));
    }

    public function testGetEmptyValue()
    {
        $textLine = new TextLine();

        $this->assertTrue($textLine->isEmpty($textLine->getEmptyValue()), 'Empty value should be marked as empty.');
    }
}
