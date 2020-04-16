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
use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldDefinitionGroupsTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;

class EnhancedRelationListFieldDefinitionGroupsTypeTest extends TestCase
{
    protected function getLanguageServiceMock()
    {
        $builder = $this->createMock(LanguageService::class);

        $builder->method('loadLanguage')->willReturnCallback(
            static function ($languageCode) {
                if ($languageCode !== 'eng-GB') {
                    throw new NotFoundException('not found', $languageCode);
                }

                return new Language([
                    'id' => 1,
                    'languageCode' => $languageCode,
                    'name' => 'English (United Kingdom)',
                    'enabled' => true,
                ]);
            }
        );

        return $builder;
    }

    public function testGetName()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionGroupsType($this->getLanguageServiceMock(), $configResolver);

        $this->assertEquals('intprogenhancedrelationlist_definition_groups', $type->getName());
    }

    public function testGetParent()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionGroupsType($this->getLanguageServiceMock(), $configResolver);

        $this->assertEquals(HiddenType::class, $type->getParent());
    }

    public function testGetBlockPrefix()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type = new EnhancedRelationListFieldDefinitionGroupsType($this->getLanguageServiceMock(), $configResolver);

        $this->assertEquals('intprogenhancedrelationlist_definition_groups', $type->getBlockPrefix());
    }

    public function testBuildForm()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB']);

        $type    = new EnhancedRelationListFieldDefinitionGroupsType($this->getLanguageServiceMock(), $configResolver);
        $builder = $this->createMock(FormBuilder::class);
        $builder->expects($this->once())->method('addModelTransformer')->with(new FieldDefinitionGroupsTransformer());

        $type->buildForm($builder, []);
    }

    public function testFinishView()
    {
        $configResolver = $this->createMock(ConfigResolverInterface::class);
        $configResolver->method('getParameter')->willReturn(['eng-GB', 'fre-FR']);

        $view = new FormView();
        $type = new EnhancedRelationListFieldDefinitionGroupsType($this->getLanguageServiceMock(), $configResolver);
        $form = $this->createMock(Form::class);
        $normData  = ['some', 'array'];
        $form->expects($this->once())->method('getNormData')->willReturn(json_encode($normData));

        $type->finishView($view, $form, []);

        $this->assertEquals(
            [
                'value'       => null,
                'attr'        => [],
                'array_data'  => $normData,
                'languageMap' => [
                    [
                        'code' => 'eng-GB',
                        'name' => 'English (United Kingdom)'
                    ]
                ],
            ],
            $view->vars
        );
    }
}
