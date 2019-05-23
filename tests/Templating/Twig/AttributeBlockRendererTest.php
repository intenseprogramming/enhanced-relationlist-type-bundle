<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-05-19 03:24 AM
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Templating\Twig;

use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Boolean;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\Integer;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\TextLine;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Template;

/**
 * Class AttributeBlockRendererTest.
 *
 * @package   IntProg\EnhancedRelationListBundle\Templating\Twig
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class AttributeBlockRendererTest extends TestCase
{
    public function testRenderAttributeView()
    {
        $environment         = $this->createMock(Environment::class);
        $baseTemplate        = $this->createMock(Template::class);
        $viewTemplate1       = $this->createMock(Template::class);
        $viewTemplate2Parent = $this->createMock(Template::class);
        $viewTemplate2       = $this->createMock(Template::class);
        $viewTemplate3       = $this->createMock(Template::class);

        $templateList = [
            'the_base_template' => $baseTemplate,
            'template_1'        => $viewTemplate1,
            'template_2'        => $viewTemplate2,
            'template_3'        => $viewTemplate3,
        ];

        $stringBlock  = ['string block'];
        $integerBlock = ['integer block'];

        $integerAttribute  = new Integer(['value' => 312]);
        $integerDefinition = ['attribute_definition'];
        $integerParams     = ['some_parameter' => 'integer'];
        $stringAttribute   = new TextLine(['value' => 312]);
        $stringDefinition  = ['attribute_definition'];
        $stringParams      = ['some_parameter' => 'string'];
        $booleanAttribute  = new Boolean(['value' => true]);
        $booleanDefinition = ['attribute_definition'];
        $booleanParams     = ['some_parameter' => 'boolean'];

        $environment->method('loadTemplate')->willReturnCallback(
            function ($templateName) use ($templateList) {
                return $templateList[$templateName];
            }
        );
        $environment->method('mergeGlobals')->willReturnCallback(
            function ($parameters) {
                return $parameters;
            }
        );
        $viewTemplate1->method('getBlocks')->willReturn([]);
        $viewTemplate2->method('getBlocks')->willReturn([]);
        $viewTemplate2->method('getParent')->willReturn($viewTemplate2Parent);
        $viewTemplate2Parent->method('getBlocks')->willReturn(['string_relation_attribute' => $stringBlock]);
        $viewTemplate3->method('getBlocks')->willReturn(['integer_relation_attribute' => $integerBlock]);
        $baseTemplate->method('hasBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return isset($blocks[$blockName]);
            }
        );
        $baseTemplate->method('renderBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return $blocks[$blockName];
            }
        );

        $renderer = new AttributeBlockRenderer();
        $renderer->setTwig($environment);
        $renderer->setBaseTemplate('the_base_template');
        $renderer->setAttributeViewResources([
            ['template' => 'template_1', 'priority' => 1],
            ['template' => 'template_2', 'priority' => 3],
            ['template' => 'template_3', 'priority' => 2],
        ]);

        $this->assertEquals($integerBlock, $renderer->renderAttributeView($integerAttribute, $integerDefinition, $integerParams));
        $this->assertEquals($stringBlock, $renderer->renderAttributeView($stringAttribute, $stringDefinition, $stringParams));
        $this->assertEquals($stringBlock, $renderer->renderAttributeView($stringAttribute, $stringDefinition, $stringParams));

        $this->expectExceptionMessage('boolean_relation_attribute');
        $renderer->renderAttributeView($booleanAttribute, $booleanDefinition, $booleanParams);
    }

    public function testRenderAttributeDefinitionView()
    {
        $environment         = $this->createMock(Environment::class);
        $baseTemplate        = $this->createMock(Template::class);
        $viewTemplate1       = $this->createMock(Template::class);
        $viewTemplate2Parent = $this->createMock(Template::class);
        $viewTemplate2       = $this->createMock(Template::class);
        $viewTemplate3       = $this->createMock(Template::class);

        $templateList = [
            'the_base_template' => $baseTemplate,
            'template_1'        => $viewTemplate1,
            'template_2'        => $viewTemplate2,
            'template_3'        => $viewTemplate3,
        ];

        $stringBlock  = ['string block'];
        $integerBlock = ['integer block'];

        $integerDefinition = ['attribute_definition'];
        $integerParams     = ['some_parameter' => 'integer'];
        $stringDefinition  = ['attribute_definition'];
        $stringParams      = ['some_parameter' => 'string'];
        $booleanDefinition = ['attribute_definition'];
        $booleanParams     = ['some_parameter' => 'boolean'];

        $environment->method('loadTemplate')->willReturnCallback(
            function ($templateName) use ($templateList) {
                return $templateList[$templateName];
            }
        );
        $environment->method('mergeGlobals')->willReturnCallback(
            function ($parameters) {
                return $parameters;
            }
        );
        $viewTemplate1->method('getBlocks')->willReturn([]);
        $viewTemplate2->method('getBlocks')->willReturn([]);
        $viewTemplate2->method('getParent')->willReturn($viewTemplate2Parent);
        $viewTemplate2Parent->method('getBlocks')->willReturn(['string_relation_attribute_definition' => $stringBlock]);
        $viewTemplate3->method('getBlocks')->willReturn(['integer_relation_attribute_definition' => $integerBlock]);
        $baseTemplate->method('hasBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return isset($blocks[$blockName]);
            }
        );
        $baseTemplate->method('renderBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return $blocks[$blockName];
            }
        );

        $renderer = new AttributeBlockRenderer();
        $renderer->setTwig($environment);
        $renderer->setBaseTemplate('the_base_template');
        $renderer->setAttributeDefinitionViewResources([
            ['template' => 'template_1', 'priority' => 1],
            ['template' => 'template_2', 'priority' => 3],
            ['template' => 'template_3', 'priority' => 2],
        ]);

        $this->assertEquals($integerBlock, $renderer->renderAttributeDefinitionView('integer', $integerDefinition, $integerParams));
        $this->assertEquals($stringBlock, $renderer->renderAttributeDefinitionView('string', $stringDefinition, $stringParams));
        $this->assertEquals($stringBlock, $renderer->renderAttributeDefinitionView('string', $stringDefinition, $stringParams));
        $this->assertEquals('', $renderer->renderAttributeDefinitionView('boolean', $booleanDefinition, $booleanParams));
    }

    public function testLocalTemplates()
    {
        $environment             = $this->createMock(Environment::class);
        $baseTemplate            = $this->createMock(Template::class);
        $localAttributeTemplate  = $this->createMock(Template::class);
        $localDefinitionTemplate = $this->createMock(Template::class);

        $templateList = [
            'the_base_template'         => $baseTemplate,
            'local_attribute_template'  => $localAttributeTemplate,
            'local_definition_template' => $localDefinitionTemplate,
        ];

        $attributeBlock = ['integer block'];
        $definitionBlock = ['integer block'];

        $renderer = new AttributeBlockRenderer();
        $renderer->setTwig($environment);
        $renderer->setBaseTemplate('the_base_template');

        $integerAttribute     = new Integer(['value' => 312]);
        $integerDefinition    = ['attribute_definition'];
        $integerParams        = ['some_parameter' => 'integer'];

        $environment->method('loadTemplate')->willReturnCallback(
            function ($templateName) use ($templateList) {
                return $templateList[$templateName];
            }
        );
        $environment->method('mergeGlobals')->willReturnCallback(
            function ($parameters) {
                return $parameters;
            }
        );
        $localAttributeTemplate->method('getBlocks')->willReturn(['integer_relation_attribute' => $attributeBlock]);
        $localDefinitionTemplate->method('getBlocks')->willReturn(['integer_relation_attribute_definition' => $definitionBlock]);
        $baseTemplate->method('hasBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return isset($blocks[$blockName]);
            }
        );
        $baseTemplate->method('renderBlock')->willReturnCallback(
            function ($blockName, $context, $blocks) {
                return $blocks[$blockName];
            }
        );

        $this->assertEquals($attributeBlock, $renderer->renderAttributeView($integerAttribute, $integerDefinition, $integerParams + ['template' => 'local_attribute_template']));
        $this->assertEquals($definitionBlock, $renderer->renderAttributeDefinitionView('integer', $integerDefinition, $integerParams + ['template' => 'local_definition_template']));
    }
}
