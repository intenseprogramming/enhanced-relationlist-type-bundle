<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 18:18
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use DOMDocument;
use DOMElement;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter as ConverterInterface;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

/**
 * Class Converter.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Converter implements ConverterInterface
{
    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param FieldValue        $value
     * @param StorageFieldValue $storageFieldValue
     *
     * @return void
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        $doc      = new DOMDocument('1.0', 'utf-8');
        $treeNode = $doc->createElement('relation-list');

        if (is_array($value->data['relations'] ?? false) && !empty($value->data['relations'])) {
            $relationsNode = $doc->createElement('relations');

            foreach ($value->data['relations'] as $relation) {
                $relationsNode->appendChild($this->createRelationElement($doc, $relation));
            }

            $treeNode->appendChild($relationsNode);
        }

        if (is_array($value->data['groups'] ?? false) && !empty($value->data['groups'])) {
            $groupsNode = $doc->createElement('groups');

            foreach ($value->data['groups'] as $groupName => $group) {
                $groupNode = $doc->createElement('group');
                $groupNode->setAttribute('name', $groupName);

                foreach ($group as $relation) {
                    $groupNode->appendChild($this->createRelationElement($doc, $relation));
                }

                $groupsNode->appendChild($groupNode);
            }

            $treeNode->appendChild($groupsNode);
        }

        $doc->appendChild($treeNode);
        $dataText = $doc->saveXML();

        $storageFieldValue->dataText = $dataText;
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param StorageFieldValue $value
     * @param FieldValue        $fieldValue
     *
     * @return void
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        if (empty($value->dataText) || $dom->loadXML($value->dataText) !== true) {
            return;
        }

        $fieldValue->data = [
            'relations' => [],
            'groups'    => [],
        ];

        if (
            ($relations = $dom->getElementsByTagName('relations')) &&
            $relations->length == 1
        ) {
            foreach ($relations->item(0)->getElementsByTagName('relation') as $relationNode) {
                $fieldValue->data['relations'][] = $this->resolveRelationElement($relationNode);
            };
        }

        if (
            ($groups = $dom->getElementsByTagName('groups')) &&
            $groups->length > 0
        ) {
            /** @var DOMElement $group */
            foreach ($groups->item(0)->getElementsByTagName('group') as $group) {
                $groupName = $group->getAttribute('name');
                $fieldValue->data['groups'][$groupName] = [];

                foreach ($group->getElementsByTagName('relation') as $relation) {
                    $fieldValue->data['groups'][$groupName][] = $this->resolveRelationElement($relation);
                }
            }
        }

        return;
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef.
     *
     * @param FieldDefinition        $fieldDef
     * @param StorageFieldDefinition $storageDef
     *
     * @return void
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings   = $fieldDef->fieldTypeConstraints->fieldSettings;
        $fieldValidators = $fieldDef->fieldTypeConstraints->validators;

        if (empty($fieldSettings) || empty($fieldValidators)) {
            return;
        }

        $doc                  = new DOMDocument('1.0', 'utf-8');
        $enhancedRelationList = $doc->createElement('enhanced_relation_list');
        $settings             = $doc->createElement('settings');
        $validators           = $doc->createElement('validators');

        foreach ($fieldSettings['attributeDefinitions'] as $identifier => $attributeDefinitionValue) {
            $attributeDefinition = $doc->createElement('attribute_definition');
            $attributeDefinition->setAttribute('identifier', $identifier);
            $attributeDefinition->setAttribute('type', $attributeDefinitionValue['type']);
            $attributeDefinition->setAttribute('required', $attributeDefinitionValue['required'] ? 1 : 0);

            foreach ($attributeDefinitionValue['name'] as $languageCode => $name) {
                $nameElement = $doc->createElement('attribute_name');
                $nameElement->setAttribute('language-code', $languageCode);
                $nameElement->setAttribute('value', $name);
                $attributeDefinition->appendChild($nameElement);
            }

            $attributeSettings = $doc->createElement('settings');
            $attributeSettings->appendChild($doc->createTextNode(json_encode($attributeDefinitionValue['settings'] ?? [])));
            $attributeDefinition->appendChild($attributeSettings);

            $settings->appendChild($attributeDefinition);
        }
        if ($fieldSettings['defaultBrowseLocation']) {
            $node = $doc->createElement('default_browse_location');
            $node->setAttribute('location-id', $fieldSettings['defaultBrowseLocation']);
            $settings->appendChild($node);
        }
        if ($fieldSettings['selectionLimit']) {
            $node = $doc->createElement('selection_limit');
            $node->setAttribute('value', $fieldSettings['selectionLimit']);
            $settings->appendChild($node);
        }

        $node = $doc->createElement('group_setting');
        $node->setAttribute('position_fixed', $fieldSettings['groupSettings']['positionsFixed'] ? 1 : 0);
        $node->setAttribute('extendable', $fieldSettings['groupSettings']['extendable'] ? 1 : 0);
        $node->setAttribute('allow_ungrouped', $fieldSettings['groupSettings']['allowUngrouped'] ? 1 : 0);
        $node->setAttribute('groups', json_encode($fieldSettings['groupSettings']['groups'] ?? []));

        $settings->appendChild($node);

        foreach ($fieldValidators['relationValidator']['allowedContentTypes'] as $contentTypeIdentifier) {
            $attributeDefinition = $doc->createElement('allowed_content_type');
            $attributeDefinition->setAttribute('content-type-identifier', $contentTypeIdentifier);

            $validators->appendChild($attributeDefinition);
        }

        $enhancedRelationList->appendChild($settings);
        $enhancedRelationList->appendChild($validators);
        $doc->appendChild($enhancedRelationList);
        $storageDef->dataText5 = $doc->saveXML();
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @param StorageFieldDefinition $storageDef
     * @param FieldDefinition        $fieldDef
     *
     * @return void
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        // default settings
        $fieldDef->fieldTypeConstraints->fieldSettings = [
            'attributeDefinitions'  => [],
            'defaultBrowseLocation' => null,
            'selectionLimit'        => 0,
            'groupSettings'         => [
                'positionsFixed' => false,
                'extendable'     => true,
                'allowUngrouped' => true,
                'groups'         => [],
            ],
        ];

        $fieldDef->fieldTypeConstraints->validators = [
            'relationValidator' => [
                'allowedContentTypes' => [],
            ],
        ];

        $dom = new DOMDocument('1.0', 'utf-8');
        if (empty($storageDef->dataText5) || $dom->loadXML($storageDef->dataText5) !== true) {
            return;
        }

        // read settings from storage
        $fieldSettings = &$fieldDef->fieldTypeConstraints->fieldSettings;

        /** @var DOMElement $attributeDefinition */
        foreach ($dom->getElementsByTagName('attribute_definition') as $attributeDefinition) {
            $names = [];

            /** @var DOMElement $attributeName */
            foreach ($attributeDefinition->getElementsByTagName('attribute_name') as $attributeName) {
                $names[$attributeName->getAttribute('language-code')] = $attributeName->getAttribute('value');
            }

            $settings         = [];
            $settingsElements = $attributeDefinition->getElementsByTagName('settings');
            if ($settingsElements->length) {
                $settings = @json_decode($settingsElements->item(0)->textContent, true);
            }

            $fieldSettings['attributeDefinitions'][$attributeDefinition->getAttribute('identifier')] = [
                'type'     => $attributeDefinition->getAttribute('type'),
                'name'     => $names,
                'required' => !!$attributeDefinition->getAttribute('required'),
                'settings' => $settings,
            ];
        }
        if (
            ($defaultBrowseLocation = $dom->getElementsByTagName('default_browse_location')->item(0)) &&
            $defaultBrowseLocation->hasAttribute('location-id')
        ) {
            $fieldSettings['defaultBrowseLocation'] = $defaultBrowseLocation->getAttribute('location-id');
        }
        if (
            ($selectionLimit = $dom->getElementsByTagName('selection_limit')->item(0)) &&
            $selectionLimit->hasAttribute('value')
        ) {
            $fieldSettings['selectionLimit'] = $selectionLimit->getAttribute('value');
        }

        if (
            $dom->getElementsByTagName('group_setting')->length &&
            ($groupSettingElement = $dom->getElementsByTagName('group_setting')->item(0))
        ) {
            if ($groupSettingElement->hasAttribute('position_fixed')) {
                $fieldSettings['groupSettings']['positionsFixed'] =
                    !!$groupSettingElement->getAttribute('position_fixed');
            }
            if ($groupSettingElement->hasAttribute('extendable')) {
                $fieldSettings['groupSettings']['extendable'] =
                    !!$groupSettingElement->getAttribute('extendable');
            }
            if ($groupSettingElement->hasAttribute('allow_ungrouped')) {
                $fieldSettings['groupSettings']['allowUngrouped'] =
                    !!$groupSettingElement->getAttribute('allow_ungrouped');
            }
            if ($groupSettingElement->hasAttribute('groups')) {
                $groups = json_decode($groupSettingElement->getAttribute('groups'));

                if (is_array($groups)) {
                    $fieldSettings['groupSettings']['groups'] = $groups;
                }
            }
        }

        // read validators configuration from storage
        $validators = &$fieldDef->fieldTypeConstraints->validators;
        /** @var DOMElement $contentTypeElement */
        foreach ($dom->getElementsByTagName('allowed_content_type') as $contentTypeElement) {
            if ($contentTypeElement->hasAttribute('content-type-identifier')) {
                $validators['relationValidator']['allowedContentTypes'][] =
                    $contentTypeElement->getAttribute('content-type-identifier');
            }
        }
    }

    /**
     * Returns the name of the index column in the attribute table.
     *
     * @return false|string
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }

    /**
     * Creates a dom element containing the relation information.
     *
     * @param DOMDocument $document The dom document to create the element.
     * @param array       $relation The relation in array form.
     *
     * @return DOMElement
     */
    protected function createRelationElement(DOMDocument $document, array $relation)
    {
        $relationNode = $document->createElement('relation');
        $relationNode->setAttribute('content-id', $relation['contentId']);

        foreach ($relation['attributes'] as $identifier => $attribute) {
            $attributeNode = $document->createElement('attribute', json_encode($attribute['value']));
            $attributeNode->setAttribute('identifier', $identifier);
            $attributeNode->setAttribute('type', $attribute['type']);

            $relationNode->appendChild($attributeNode);
        }

        return $relationNode;
    }

    /**
     * Parses a dom element created by "createRelationElement" and returns the original value.
     *
     * @param DOMElement $element The dom element from the stored xml.
     *
     * @return array
     */
    protected function resolveRelationElement(DOMElement $element)
    {
        $relation = [
            'contentId'  => $element->getAttribute('content-id'),
            'group'      => null,
            'attributes' => [],
        ];

        /** @var DOMElement $attribute */
        foreach ($element->getElementsByTagName('attribute') as $attribute) {
            $relation['attributes'][$attribute->getAttribute('identifier')] = [
                'type'  => $attribute->getAttribute('type'),
                'value' => json_decode($attribute->textContent, true),
            ];
        }

        return $relation;
    }
}
