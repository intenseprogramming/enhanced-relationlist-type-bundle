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

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\Mapper\AbstractRelationFormMapper;
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
class Mapper extends AbstractRelationFormMapper
{
    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'selectionDefaultLocation',
                HiddenType::class,
                [
                    'required'      => false,
                    'property_path' => 'fieldSettings[selectionDefaultLocation]',
                    'label'         => 'field_definition.intprogenhancedrelationlist.selection_default_location',
                ]
            )
            ->add(
                'selectionContentTypes',
                ChoiceType::class,
                [
                    'choices'           => $this->getContentTypesHash(),
                    'choices_as_values' => true,
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

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldDefinition = $data->fieldDefinition;
        $formConfig      = $fieldForm->getConfig();
        $names           = $fieldDefinition->getNames();
        $label           = $fieldDefinition->getName($formConfig->getOption('mainLanguageCode')) ?: reset($names);

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
                            'browse_location'                  => $fieldDefinition->getFieldSettings()['defaultBrowseLocation'],
                            'selection_limit'                  => $fieldDefinition->getFieldSettings()['selectionLimit'],
                            'allowed_content_type_identifiers' => $fieldDefinition->getValidatorConfiguration()['relationValidator']['allowedContentTypes'],
                            'sub_attributes'                   => $fieldDefinition->getFieldSettings()['attributeDefinitions'],
                            'data_class'                       => null,
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }
}
