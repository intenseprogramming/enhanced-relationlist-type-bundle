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

use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\Values\Content\Language;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
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
    private function getLanguageServiceMock()
    {
        $mock = $this->createMock(LanguageService::class);
        $mock->method('loadLanguage')->willReturnCallback(static function($languageCode) {
            return new Language(['languageCode' => $languageCode, 'name' => 'stub']);
        });

        return $mock;
    }

    public function testGetName()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $this->getLanguageServiceMock(),
            $configResolver
        );

        $this->assertEquals('intprogenhancedrelationlist_definition_attributes', $type->getName());
    }

    public function testGetParent()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $this->getLanguageServiceMock(),
            $configResolver
        );

        $this->assertEquals(HiddenType::class, $type->getParent());
    }

    public function testGetBlockPrefix()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $this->getLanguageServiceMock(),
            $configResolver
        );

        $this->assertEquals('intprogenhancedrelationlist_definition_attributes', $type->getBlockPrefix());
    }

    public function testBuildForm()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type    = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $this->getLanguageServiceMock(),
            $configResolver
        );
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->with(new FieldDefinitionAttributesTransformer());

        $type->buildForm($builder, []);
    }

    public function testMissingButConfiguredLanguage()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB', 'fre-FR']);

        $languageService = $this->createMock(LanguageService::class);
        $languageService->expects($this->at(0))->method('loadLanguage')->willReturnCallback(
            static function($languageCode) {
                return new Language(['languageCode' => $languageCode, 'name' => 'stub']);
            }
        );
        $languageService->expects($this->at(1))->method('loadLanguage')->will(
            $this->throwException(new NotFoundException('language not found', 'fre-FR'))
        );

        $view = new FormView();
        $type = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $languageService,
            $configResolver
        );
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
                'languageMap'             => [['code' => 'eng-GB', 'name' => 'stub']],
            ],
            $view->vars
        );
    }

    public function testBuildView()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $view = new FormView();
        $type = new EnhancedRelationListFieldDefinitionAttributesType(
            $this->getTransformer(),
            $this->getLanguageServiceMock(),
            $configResolver
        );
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
                'languageMap'             => [['code' => 'eng-GB', 'name' => 'stub']],
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
