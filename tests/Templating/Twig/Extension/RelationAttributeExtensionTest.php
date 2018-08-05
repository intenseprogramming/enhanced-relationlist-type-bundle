<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 11:08
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Templating\Twig\Extension;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_SimpleFunction;
use Twig_Template;

class RelationAttributeExtensionTest extends TestCase
{
    public function testGetFunctions()
    {
        $extension = new RelationAttributeExtension();

        /** @var Twig_SimpleFunction[] $functions */
        $functions     = $extension->getFunctions();
        $functionNames = [];

        foreach ($functions as $function) {
            $functionNames[] = $function->getName();
        }

        $this->assertTrue(in_array('erl_render_attribute', $functionNames));
        $this->assertTrue(in_array('erl_render_attribute_input', $functionNames));
        $this->assertTrue(in_array('erl_render_attribute_definition', $functionNames));
        $this->assertTrue(in_array('erl_render_attribute_definition_input', $functionNames));
    }

    public function testRenderAttribute()
    {
        $field      = new Field();
        $integer    = new Integer();
        $definition = ['type' => 'integer'];
        $extension  = new RelationAttributeExtension();

        $twig     = $this->createMock(Twig_Environment::class);
        $template = $this->createMock(Twig_Template::class);

        $twig->expects($this->once())->method('load')->with('IntProgEnhancedRelationListBundle::erl_attributes.html.twig')->willReturn($template);
        $template->expects($this->once())->method('renderBlock')->with('integer_relation_attribute', [
            'attribute'  => $integer,
            'definition' => $definition,
            'field'      => $field,
            'parameters' => [],
        ]);

        /** @var Twig_SimpleFunction $function */
        foreach ($extension->getFunctions() as $function) {
            if ($function->getName() == 'erl_render_attribute') {
                $callable = $function->getCallable();

                $callable($twig, $field, $integer, $definition);
            }
        }
    }

    public function testRenderAttributeInput()
    {
        $field      = new Field();
        $integer    = ['value' => null];
        $definition = ['type' => 'integer'];
        $extension  = new RelationAttributeExtension();

        $twig     = $this->createMock(Twig_Environment::class);
        $template = $this->createMock(Twig_Template::class);

        $twig->expects($this->once())->method('load')->with('IntProgEnhancedRelationListBundle::erl_attributes_edit.html.twig')->willReturn($template);
        $template->expects($this->once())->method('renderBlock')->with('integer_relation_attribute_edit', [
            'value'      => $integer,
            'definition' => $definition,
            'field'      => $field,
            'parameters' => [],
            'hasErrors'  => true,
        ]);

        /** @var Twig_SimpleFunction $function */
        foreach ($extension->getFunctions() as $function) {
            if ($function->getName() == 'erl_render_attribute_input') {
                $callable = $function->getCallable();

                $callable($twig, $field, $definition, $integer, ['errors' => ['some error']]);
            }
        }
    }

    public function testRenderAttributeDefinition()
    {
        $fieldDefinition = new FieldDefinition();
        $definition      = ['type' => 'integer'];
        $extension       = new RelationAttributeExtension();

        $twig     = $this->createMock(Twig_Environment::class);
        $template = $this->createMock(Twig_Template::class);

        $twig->expects($this->once())->method('load')->with('IntProgEnhancedRelationListBundle::erl_attributes_definition.html.twig')->willReturn($template);
        $template->expects($this->once())->method('renderBlock')->with('integer_relation_attribute_definition', [
            'definition'      => $definition,
            'fieldDefinition' => $fieldDefinition,
            'parameters'      => [],
        ]);

        /** @var Twig_SimpleFunction $function */
        foreach ($extension->getFunctions() as $function) {
            if ($function->getName() == 'erl_render_attribute_definition') {
                $callable = $function->getCallable();

                $callable($twig, $fieldDefinition, $definition, []);
            }
        }
    }

    public function testRenderAttributeDefinitionInput()
    {
        $fieldDefinition = new FieldDefinition();
        $definition      = ['type' => 'integer'];
        $extension       = new RelationAttributeExtension();

        $twig     = $this->createMock(Twig_Environment::class);
        $template = $this->createMock(Twig_Template::class);

        $twig->expects($this->once())->method('load')->with('IntProgEnhancedRelationListBundle::erl_attributes_definition_edit.html.twig')->willReturn($template);
        $template->expects($this->once())->method('renderBlock')->with('integer_relation_attribute_definition_edit', [
            'definition'      => $definition,
            'fieldDefinition' => $fieldDefinition,
            'parameters'      => [],
        ]);

        /** @var Twig_SimpleFunction $function */
        foreach ($extension->getFunctions() as $function) {
            if ($function->getName() == 'erl_render_attribute_definition_input') {
                $callable = $function->getCallable();

                $callable($twig, $fieldDefinition, $definition, []);
            }
        }
    }
}
