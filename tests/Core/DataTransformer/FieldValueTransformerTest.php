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

use eZ\Publish\API\Repository\FieldType;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Service\AttributeConverter;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;

class FieldValueTransformerTest extends TestCase
{
    public function testTransformation()
    {
        $transformer   = new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);
        $configuration = [
            'boolean'   => [
                'type' => 'boolean',
            ],
            'integer'   => [
                'type' => 'integer',
            ],
            'selection' => [
                'type' => 'selection',
            ],
            'string'    => [
                'type' => 'string',
            ],
        ];

        $transformer = new FieldValueTransformer($transformer, $configuration);

        $testData = $this->getTestData();
        $data     = $transformer->reverseTransform($transformer->transform($testData));

        $this->assertEquals($testData, $data, 'Should restore the transformed value.');
    }

    public function testRemovingUnconfiguredAttributeContent()
    {
        $transformer   = new RelationAttributeRepository([
            'boolean' => new AttributeConverter\Boolean(),
        ]);
        $configuration = [
            'boolean' => ['type' => 'boolean'],
        ];
        $transformer   = new FieldValueTransformer($transformer, $configuration);

        $rawRelation      = new Value\Relation([
            'contentId'  => 22,
            'attributes' => [
                'boolean' => new Attribute\Boolean(['value' => false]),
                'string'  => new Attribute\TextLine(['value' => 'test line']),
            ],
        ]);
        $expectedRelation = new Value\Relation([
            'contentId'  => 22,
            'attributes' => ['boolean' => new Attribute\Boolean(['value' => false])],
        ]);

        $transformValue = new Value([$rawRelation], [new Value\Group('group', [$rawRelation])]);
        $resultValue    = $transformer->reverseTransform($transformer->transform($transformValue));

        $this->assertEquals([$expectedRelation], $resultValue->relations, 'Transformer should clean relations.');
        $this->assertEquals([$expectedRelation], $resultValue->groups['group']->relations, 'Transformer should clean relations of groups.');

        $rawTransformedValue =
            '[{"contentId":22,"attributes":{"boolean":0,"string":"test line"}},' .
            '{"group":"group"},' .
            '{"contentId":22,"attributes":{"boolean":0,"string":"test line"}}]';
        $resultValue         = $transformer->reverseTransform($rawTransformedValue);

        $this->assertEquals([$expectedRelation], $resultValue->relations, 'Reverse transform should clean relations.');
        $this->assertEquals([$expectedRelation], $resultValue->groups['group']->relations, 'Reverse transform should clean relations of groups.');
    }

    public function testInvalidTransformation()
    {
        $transformer   = $this->createMock(RelationAttributeRepository::class);
        $configuration = [];

        $transformer = new FieldValueTransformer($transformer, $configuration);

        $this->assertEquals($transformer->transform(false), null, 'Should return null on invalid data to transform.');
        $this->assertEquals($transformer->reverseTransform(''), new Value(), 'Should return empty value on invalid data to reverse transform.');
    }

    protected function getTestData()
    {
        return new Value(
            [
                new Value\Relation(
                    [
                        'contentId'  => 1,
                        'attributes' => [
                            'boolean'   => new Attribute\Boolean(['value' => true]),
                            'integer'   => new Attribute\Integer(['value' => '123']),
                            'selection' => new Attribute\Selection(['selection' => ['2']]),
                            'string'    => new Attribute\TextLine(['value' => 'test string']),
                        ],
                    ]
                ),
            ],
            [
                new Value\Group(
                    'test_group',
                    [
                        new Value\Relation(
                            [
                                'contentId'  => 33,
                                'attributes' => [
                                    'boolean'   => new Attribute\Boolean(['value' => false]),
                                    'integer'   => new Attribute\Integer(['value' => '321']),
                                    'selection' => new Attribute\Selection(['selection' => ['1']]),
                                    'string'    => new Attribute\TextLine(['value' => 'another test string']),
                                ],
                            ]
                        ),
                    ]
                ),
            ]
        );
    }
}
