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

use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldDefinitionGroupsTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;

class EnhancedRelationListFieldDefinitionGroupsTypeTest extends TestCase
{
    public function testGetName()
    {
        $type = new EnhancedRelationListFieldDefinitionGroupsType();

        $this->assertEquals('intprogenhancedrelationlist_definition_groups', $type->getName());
    }

    public function testGetParent()
    {
        $type = new EnhancedRelationListFieldDefinitionGroupsType();

        $this->assertEquals(HiddenType::class, $type->getParent());
    }

    public function testGetBlockPrefix()
    {
        $type = new EnhancedRelationListFieldDefinitionGroupsType();

        $this->assertEquals('intprogenhancedrelationlist_definition_groups', $type->getBlockPrefix());
    }

    public function testBuildForm()
    {
        $type    = new EnhancedRelationListFieldDefinitionGroupsType();
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->with(new FieldDefinitionGroupsTransformer());

        $type->buildForm($builder, []);
    }

    public function testFinishView()
    {
        $view = new FormView();
        $type = new EnhancedRelationListFieldDefinitionGroupsType();
        $form = $this->createMock(Form::class);
        $normData  = ['some', 'array'];
        $form->expects($this->once())->method('getNormData')->willReturn(json_encode($normData));

        $type->finishView($view, $form, []);

        $this->assertEquals(
            [
                'value'      => null,
                'attr'       => [],
                'array_data' => $normData,
            ],
            $view->vars
        );
    }
}
