<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 06:21
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\Core\Repository\ContentTypeService;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use IntProg\EnhancedRelationListBundle\Form\Type\EnhancedRelationListType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactory;

class MapperTest extends TestCase
{
    public function testMapFieldDefinitionForm()
    {
        $contentTypeGroup = new ContentTypeGroup();
        $contentType1 = new ContentType(['identifier' => 'type_1', 'names' => ['eng-GB' => 'test c']]);
        $contentType2 = new ContentType(['identifier' => 'type_2', 'names' => ['eng-GB' => 'test b']]);
        $contentType3 = new ContentType(['identifier' => 'type_3', 'names' => ['eng-GB' => 'test a']]);

        $contentTypeService = $this->createMock(ContentTypeService::class);
        $contentTypeService->expects($this->once())->method('loadContentTypeGroups')->willReturn([$contentTypeGroup]);
        $contentTypeService->expects($this->once())->method('loadContentTypes')->willReturn(
            [$contentType1, $contentType2, $contentType3]
        );

        $fieldForm = $this->createMock(Form::class);
        $fieldForm->expects($this->exactly(9))->method('add')->willReturn($fieldForm);
        $fieldForm->expects($this->at(1))->method('add')->with(
            'selectionContentTypes',
            ChoiceType::class,
            [
                'choices'           => [
                    'test a' => 'type_3',
                    'test b' => 'type_2',
                    'test c' => 'type_1',
                ],
                'choices_as_values' => true,
                'expanded'          => false,
                'multiple'          => true,
                'required'          => false,
                'property_path'     => 'validatorConfiguration[relationValidator][allowedContentTypes]',
                'label'             => 'field_definition.intprogenhancedrelationlist.selection_content_types',
            ]
        )->willReturn($fieldForm);

        $mapper = new Mapper($contentTypeService);

        $mapper->mapFieldDefinitionForm(
            $fieldForm,
            new FieldDefinitionData(['fieldDefinition' => $this->getDefinitionData()])
        );
    }

    public function testMapFieldValueForm()
    {
        $contentTypeService = $this->createMock(ContentTypeService::class);
        $fieldForm          = $this->createMock(Form::class);
        $formBuilder        = $this->createMock(FormBuilder::class);
        $formFactory        = $this->createMock(FormFactory::class);

        $fieldForm->expects($this->once())->method('getConfig')->willReturn($formBuilder);
        $fieldForm->expects($this->once())->method('add')->willReturn($formBuilder);
        $formBuilder->expects($this->once())->method('getOption')->with('mainLanguageCode')->willReturn('eng-GB');
        $formBuilder->expects($this->once())->method('getFormFactory')->willReturn($formFactory);
        $formBuilder->expects($this->once())->method('create')->with(
            'value',
            EnhancedRelationListType::class,
            [
                'required'                         => true,
                'label'                            => 'test name match',
                'label_attr'                       => ['class' => 'form-control-label'],
                'browse_location'                  => 22,
                'selection_limit'                  => 20,
                'allowed_content_type_identifiers' => [
                    'test_type_1',
                    'test_type_2',
                ],
                'sub_attributes'                   => [
                    'integer' => [
                        'type'     => 'integer',
                        'required' => false,
                        'names'    => [
                            'eng-GB' => 'Integer',
                        ],
                        'settings' => [
                            'min' => 0,
                            'max' => 123,
                        ],
                    ],
                ],
                'data_class'                       => null,
            ]
        )->willReturn($formBuilder);
        $formBuilder->expects($this->once())->method('setAutoInitialize')->willReturn($formBuilder);
        $formBuilder->expects($this->once())->method('getForm');
        $formFactory->expects($this->once())->method('createBuilder')->willReturn($formBuilder);

        $mapper    = new Mapper($contentTypeService);
        $fieldData = new FieldData([
            'fieldDefinition' => $this->getDefinitionData(),
        ]);

        $mapper->mapFieldValueForm($fieldForm, $fieldData);
    }

    public function getDefinitionData()
    {
        return new FieldDefinition([
            'names'                  => ['eng-GB' => 'test name match'],
            'isRequired'             => true,
            'fieldSettings'          => [
                'attributeDefinitions'     => [
                    'integer' => [
                        'type'     => 'integer',
                        'required' => false,
                        'names'    => [
                            'eng-GB' => 'Integer',
                        ],
                        'settings' => [
                            'min' => 0,
                            'max' => 123,
                        ],
                    ],
                ],
                'defaultBrowseLocation'    => 22,
                'selectionLimit'           => 20,
                'selectionAllowDuplicates' => true,
                'groupSettings'            => [
                    'positionsFixed' => true,
                    'extendable'     => false,
                    'allowUngrouped' => false,
                    'groups'         => [
                        'system_group' => [
                            'eng-GB' => 'System Group',
                        ],
                    ],
                ],
            ],
            'validatorConfiguration' => [
                'relationValidator' => [
                    'allowedContentTypes' => [
                        'test_type_1',
                        'test_type_2',
                    ],
                ],
            ],
        ]);
    }
}
