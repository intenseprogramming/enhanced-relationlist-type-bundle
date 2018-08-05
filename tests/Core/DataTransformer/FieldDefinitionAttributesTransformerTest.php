<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 01:57
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\DataTransformer;

use PHPUnit\Framework\TestCase;

class FieldDefinitionAttributesTransformerTest extends TestCase
{
    public function testReverseTransform()
    {
        $transformer = new FieldDefinitionAttributesTransformer();

        $testData = [
            'test' => 11
        ];

        self::assertEquals(
            $testData,
            $transformer->reverseTransform(json_encode($testData)),
            'Should resolve value using json decode'
        );

    }

    public function testTransform()
    {
        $transformer = new FieldDefinitionAttributesTransformer();

        $testData = [
            'test' => 11
        ];

        self::assertEquals(
            json_encode($testData),
            $transformer->transform($testData),
            'Should resolve value using json decode'
        );
    }
}
