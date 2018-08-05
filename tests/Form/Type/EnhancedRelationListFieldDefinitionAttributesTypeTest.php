<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 11:29
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Form\Type;

use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldDefinitionAttributesTransformer;
use IntProg\EnhancedRelationListBundle\Service\AttributeConverter;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;

class EnhancedRelationListFieldDefinitionAttributesTypeTest extends TestCase
{
    public function testGetName()
    {
        $type = new EnhancedRelationListFieldDefinitionAttributesType($this->getTransformer());

        $this->assertEquals('intprogenhancedrelationlist_definition_attributes', $type->getName());
    }

    public function testGetParent()
    {
        $type = new EnhancedRelationListFieldDefinitionAttributesType($this->getTransformer());

        $this->assertEquals(HiddenType::class, $type->getParent());
    }

    public function testGetBlockPrefix()
    {
        $type = new EnhancedRelationListFieldDefinitionAttributesType($this->getTransformer());

        $this->assertEquals('intprogenhancedrelationlist_definition_attributes', $type->getBlockPrefix());
    }

    public function testBuildForm()
    {
        $type    = new EnhancedRelationListFieldDefinitionAttributesType($this->getTransformer());
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->with(new FieldDefinitionAttributesTransformer());

        $type->buildForm($builder, []);
    }

    public function testBuildView()
    {
        $view = new FormView();
        $type = new EnhancedRelationListFieldDefinitionAttributesType($this->getTransformer());
        $form = $this->createMock(Form::class);
        $form->expects($this->once())->method('getData')->willReturn('data sample');

        $type->buildView($view, $form, []);

        $this->assertEquals(
            [
                'value'                   => null,
                'attr'                    => [],
                'availableAttributeTypes' => [
                    'boolean',
                    'integer',
                    'selection',
                    'string',
                ],
                'attributesData'          => 'data sample',
            ],
            $view->vars
        );
    }

    protected function getTransformer()
    {
        return new RelationAttributeRepository([
            'boolean'   => new AttributeConverter\Boolean(),
            'integer'   => new AttributeConverter\Integer(),
            'selection' => new AttributeConverter\Selection(),
            'string'    => new AttributeConverter\TextLine(),
        ]);
    }
}
