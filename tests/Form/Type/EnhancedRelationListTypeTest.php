<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 10:33
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Form\Type;

use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Repository\ContentService;
use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldValueTransformer;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnhancedRelationListTypeTest extends TestCase
{
    public function testGetBlockPrefix()
    {
        $type = $this->getBlankEnhancedRelationListType();

        $this->assertEquals('intprogenhancedrelationlist', $type->getBlockPrefix());
    }

    public function testConfigureOptions()
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver->expects($this->exactly(4))->method('setDefault')->withConsecutive(
            ['browse_location', 1],
            ['selection_limit', 0],
            ['allowed_content_type_identifiers', []],
            ['sub_attributes', []]
        );

        $type = $this->getBlankEnhancedRelationListType();

        $type->configureOptions($resolver);
    }

    public function testFinishView()
    {
        $view = new FormView();
        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('getNormData')->willReturn(json_encode([
            ['contentId' => 12],
            ['group' => 'some_group'],
        ]));
        $form->expects($this->once())->method('getData')->willReturn(new Value([]));

        $contentService = $this->createMock(ContentService::class);
        $contentService->expects($this->once())->method('loadContent')->with(12)->willReturn('content target');

        $type = new EnhancedRelationListType(
            $contentService,
            new RelationAttributeRepository([])
        );

        $type->finishView(
            $view,
            $form,
            [
                'selection_limit'                  => 0,
                'browse_location'                  => 2,
                'allowed_content_type_identifiers' => [],
            ]
        );

        $this->assertEquals(
            [
                'value'                            => null,
                'attr'                             => [],
                'limit'                            => 0,
                'default_location'                 => 2,
                'allowed_content_type_identifiers' => [],
                'array_data'                       => [
                    [
                        'contentId' => 12,
                        'vars'      => [
                            'content' => 'content target',
                        ],
                    ],
                    [
                        'group' => 'some_group',
                    ],
                ],
                'attribute_errors'                 => [],
            ],
            $view->vars
        );
    }

    public function testFinishViewWithCustomErrors()
    {
        $view = new FormView();
        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('getNormData')->willReturn(json_encode([
            ['contentId' => 12],
            ['group' => 'some_group'],
        ]));

        $value                  = new Value([]);
        $validationErrors       = [
            ['attribute_identifier' => [new ValidationError('some error %replace%', null, ['%replace%' => 'replaced'])]]
        ];
        $value->attributeErrors = $validationErrors;

        $form->expects($this->once())->method('getData')->willReturn($value);

        $contentService = $this->createMock(ContentService::class);
        $contentService->expects($this->once())->method('loadContent')->with(12)->willReturn('content target');

        $type = new EnhancedRelationListType(
            $contentService,
            new RelationAttributeRepository([])
        );

        $type->finishView(
            $view,
            $form,
            [
                'selection_limit'                  => 0,
                'browse_location'                  => 2,
                'allowed_content_type_identifiers' => [],
            ]
        );

        $this->assertEquals(
            [
                'value'                            => null,
                'attr'                             => [],
                'limit'                            => 0,
                'default_location'                 => 2,
                'allowed_content_type_identifiers' => [],
                'array_data'                       => [
                    [
                        'contentId' => 12,
                        'vars'      => [
                            'content' => 'content target',
                        ],
                    ],
                    [
                        'group' => 'some_group',
                    ],
                ],
                'attribute_errors'                 => [
                    ['attribute_identifier' => ['some error replaced']]
                ],
            ],
            $view->vars
        );
    }

    public function testGetName()
    {
        $type = $this->getBlankEnhancedRelationListType();

        $this->assertEquals('intprogenhancedrelationlist', $type->getName());
    }

    public function testBuildForm()
    {
        $transformer = new RelationAttributeRepository([]);

        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->with(
            new FieldValueTransformer($transformer, [])
        );

        $type = new EnhancedRelationListType(
            $this->createMock(ContentService::class),
            $transformer
        );

        $type->buildForm($builder, ['sub_attributes' => []]);
    }

    public function testGetParent()
    {
        $type = $this->getBlankEnhancedRelationListType();

        $this->assertEquals(TextType::class, $type->getParent());
    }

    protected function getBlankEnhancedRelationListType()
    {
        return new EnhancedRelationListType(
            $this->createMock(ContentService::class),
            new RelationAttributeRepository([])
        );
    }
}
