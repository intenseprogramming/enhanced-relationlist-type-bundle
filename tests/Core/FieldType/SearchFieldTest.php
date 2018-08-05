<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 09:18
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Group;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;

class SearchFieldTest extends TestCase
{
    public function testGetDefaultMatchField()
    {
        $searchField = new SearchField();

        $this->assertEquals('value', $searchField->getDefaultMatchField());
    }

    public function testGetIndexDefinition()
    {
        $searchField = new SearchField();

        $this->assertNotEmpty($searchField->getIndexDefinition());
    }

    public function testGetIndexData()
    {
        $searchField = new SearchField();
        $indexFields = $searchField->getIndexDefinition();

        $transformer = new RelationAttributeRepository([]);
        $type        = new Type($transformer);

        $value = new Value(
            [
                new Relation([
                    'contentId'  => 123,
                    'attributes' => [],
                ]),
                new Relation([
                    'contentId'  => 987,
                    'attributes' => [],
                ]),
            ],
            [
                new Group(
                    'group_name',
                    [
                        new Relation([
                            'contentId'  => 312,
                            'attributes' => [],
                        ]),
                    ]
                ),
            ]
        );

        $indexData      = $searchField->getIndexData(
            new Field(['value' => $type->toPersistenceValue($value)]),
            new FieldDefinition()
        );
        $keyedIndexData = [];

        foreach ($indexData as $field) {
            $this->assertArrayHasKey($field->name, $indexFields);

            $keyedIndexData[$field->name] = $field;
        }

        $this->assertEquals([123, 987, 312], $keyedIndexData['value']->value);
        $this->assertEquals('123-987-312', $keyedIndexData['sort_value']->value);
    }

    public function testGetDefaultSortField()
    {
        $searchField = new SearchField();

        $this->assertEquals('sort_value', $searchField->getDefaultSortField());
    }
}
