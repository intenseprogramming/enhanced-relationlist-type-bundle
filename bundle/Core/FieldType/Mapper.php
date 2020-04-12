<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 17:42
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\API\Repository\ContentTypeService;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use IntProg\EnhancedRelationListBundle\Form\Type\EnhancedRelationListFieldDefinitionAttributesType;
use IntProg\EnhancedRelationListBundle\Form\Type\EnhancedRelationListFieldDefinitionGroupsType;
use IntProg\EnhancedRelationListBundle\Form\Type\EnhancedRelationListType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormInterface;

/**
 * Class Mapper.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Mapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    /** @var ContentTypeService Used to fetch list of available content types */
    private $contentTypeService;

    /**
     * Mapper constructor.
     *
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Adds the form fields for the field definition edit.
     *
     * @param FormInterface       $fieldDefinitionForm
     * @param FieldDefinitionData $data
     *
     * @return void
     */
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $fieldDefinitionForm
            ->add(
                'defaultBrowseLocation',
                HiddenType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[defaultBrowseLocation]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.selection_default_location',
                ]
            )
            ->add(
                'selectionContentTypes',
                ChoiceType::class,
                [
                    'choices'           => $this->getContentTypesHash(),
                    'expanded'          => false,
                    'multiple'          => true,
                    'required'          => false,
                    'property_path'     => 'validatorConfiguration[relationValidator][allowedContentTypes]',
                    'label'             => 'field_definition.intprogenhancedrelationlist.selection_content_types',
                ]
            )
            ->add(
                'selectionLimit',
                IntegerType::class,
                [
                    'required'      => false,
                    'empty_data'    => 0,
                    'property_path' => 'fieldSettings[selectionLimit]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.selection_limit',
                ]
            )
            ->add(
                'selectionAllowDuplicates',
                CheckboxType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[selectionAllowDuplicates]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.selection_allow_duplicates',
                    'label_attr'    => ['class' => 'checkbox-inline'],
                ]
            )
            ->add(
                'groupSettingsPositionFixed',
                CheckboxType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[groupSettings][positionsFixed]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.group.positions_fixed',
                    'label_attr'    => ['class' => 'checkbox-inline'],
                ]
            )
            ->add(
                'groupSettingsExtendable',
                CheckboxType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[groupSettings][extendable]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.group.extendable',
                    'label_attr'    => ['class' => 'checkbox-inline'],
                ]
            )
            ->add(
                'groupSettingsAllowUngrouped',
                CheckboxType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[groupSettings][allowUngrouped]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.group.allow_ungrouped',
                    'label_attr'    => ['class' => 'checkbox-inline'],
                ]
            )
            ->add(
                'attributes',
                EnhancedRelationListFieldDefinitionAttributesType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[attributeDefinitions]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.attributes',
                ]
            )
            ->add(
                'groups',
                EnhancedRelationListFieldDefinitionGroupsType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[groupSettings][groups]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.groups',
                ]
            );
    }

    /**
     * Adds the form field to the field value edit.
     *
     * @param FormInterface $fieldForm
     * @param FieldData     $data
     *
     * @return void
     */
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig      = $fieldForm->getConfig();
        $names           = $fieldDefinition->getNames();
        $label           = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

        $settings = $fieldDefinition->getFieldSettings();
        $validator = $fieldDefinition->getValidatorConfiguration();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        EnhancedRelationListType::class,
                        [
                            'required'                         => $fieldDefinition->isRequired,
                            'label'                            => $label,
                            'label_attr'                       => ['class' => 'form-control-label'],
                            'browse_location'                  => $settings['defaultBrowseLocation'],
                            'selection_limit'                  => $settings['selectionLimit'],
                            'allowed_content_type_identifiers' => $validator['relationValidator']['allowedContentTypes'],
                            'sub_attributes'                   => $settings['attributeDefinitions'],
                            'data_class'                       => null,
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    /**
     * Fill a hash with all content types and their identifiers.
     *
     * @return array
     */
    private function getContentTypesHash(): array
    {
        $contentTypeHash = [];
        foreach ($this->contentTypeService->loadContentTypeGroups() as $contentTypeGroup) {
            foreach ($this->contentTypeService->loadContentTypes($contentTypeGroup) as $contentType) {
                $contentTypeHash[$contentType->getName()] = $contentType->identifier;
            }
        }
        ksort($contentTypeHash);

        return $contentTypeHash;
    }
}
