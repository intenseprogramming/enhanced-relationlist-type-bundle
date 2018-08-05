<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 08:47
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer;
use PHPUnit\Framework\TestCase;

class RelationAttributeRepositoryTest extends TestCase
{
    public function testFromPersistentValue()
    {
        $this->assertEquals(
            new Integer(['value' => 55]),
            $this->getTransformer()->fromPersistentValue(55, 'integer'),
            'Should generate attribute value.'
        );
    }

    public function testGetConverters()
    {
        $this->assertEquals(
            $this->getConverters(),
            $this->getTransformer()->getConverters(),
            'Should return the configured converters'
        );
    }

    public function testToPersistentValue()
    {
        $this->assertEquals(
            55,
            $this->getTransformer()->toPersistentValue(new Integer(['value' => 55])),
            'Should generate persistence value.'
        );
    }

    public function testValidate()
    {
        $this->assertEmpty($this->getTransformer()->validate(new Integer(['value' => 55]), 'integer', []));
        $this->assertCount(1, $this->getTransformer()->validate(new Integer(), 'integer', ['required' => true]));
    }

    protected function getTransformer()
    {
        return new RelationAttributeRepository($this->getConverters());
    }

    protected function getConverters()
    {
        return [
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ];
    }
}
