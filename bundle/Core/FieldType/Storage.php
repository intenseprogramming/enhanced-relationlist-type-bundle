<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-28 18:48
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\FieldType;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\FieldType\GatewayBasedStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type as FieldType;
use eZ\Publish\SPI\Persistence\Content\Type\Handler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;

/**
 * Class Storage.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class Storage extends GatewayBasedStorage
{
    /** @var Handler $contentTypeHandler */
    protected $contentTypeHandler;

    /** @var RelationAttributeRepository $repository */
    protected $repository;

    /** @var array $languages */
    protected $languages;

    /**
     * Storage constructor.
     *
     * @param Handler                     $contentTypeHandler
     * @param RelationAttributeRepository $repository
     * @param ConfigResolverInterface     $configResolver
     */
    public function __construct(
        Handler $contentTypeHandler,
        RelationAttributeRepository $repository,
        ConfigResolverInterface $configResolver
    )
    {
        $this->contentTypeHandler = $contentTypeHandler;
        $this->repository         = $repository;
        $this->languages          = $configResolver->getParameter('languages');
    }

    /**
     * Method not in use.
     *
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return null
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return null;
    }

    /**
     * Cleans up the storage data (not external source used).
     *
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return void
     *
     * @throws NotFoundException
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        $fieldDefinition = $this->contentTypeHandler->getFieldDefinition(
            $field->fieldDefinitionId,
            FieldType::STATUS_DEFINED
        );

        $groups     = $fieldDefinition->fieldTypeConstraints->fieldSettings['groupSettings']['groups'];
        $groupValue = [];
        foreach ($groups as $identifier => $group) {
            foreach ($this->languages as $language) {
                if (isset($group[$language])) {
                    $groupName = $group[$language];
                }
            }
            if (!isset($groupName)) {
                $groupName = reset($group);
            }

            if (!isset($field->value->data['groups'][$identifier])) {
                $groupValue[$identifier] = [
                    'name'      => $groupName,
                    'relations' => [],
                ];
            } else {
                $groupValue[$identifier] = [
                    'name'      => $groupName,
                    'relations' => $field->value->data['groups'][$identifier]['relations'],
                ];
            }

            unset($groupName);
        }

        foreach ($field->value->data['groups'] as $identifier => $group) {
            if (!isset($groups[$identifier])) {
                $groupValue[$identifier] = [
                    'name'      => $identifier,
                    'relations' => $group['relations'],
                ];
            }
        }

        $attributeDefinitions = $fieldDefinition->fieldTypeConstraints->fieldSettings['attributeDefinitions'];
        foreach ($groupValue as $identifier => $group) {
            foreach ($group['relations'] as $key => $relation) {
                $groupValue[$identifier]['relations'][$key] =
                    $this->normalizeRelation($relation, $attributeDefinitions);
            }
        }
        foreach ($field->value->data['relations'] as $key => $relation) {
            $field->value->data['relations'][$key] = $this->normalizeRelation($relation, $attributeDefinitions);
        }

        $field->value->data['groups'] = $groupValue;
    }

    /**
     * Method not in use.
     *
     * @param VersionInfo $versionInfo
     * @param array       $fieldIds
     * @param array       $context
     *
     * @return null
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        return null;
    }

    /**
     * Method not in use.
     *
     * @return true
     */
    public function hasFieldData()
    {
        return true;
    }

    /**
     * Method not in use.
     *
     * @param VersionInfo $versionInfo
     * @param Field       $field
     * @param array       $context
     *
     * @return bool|\eZ\Publish\SPI\Search\Field[]
     */
    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }

    /**
     * Normalizes a relation by adding missing attributes and removing undefined attributes.
     *
     * @param array $relation
     * @param array $attributeDefinitions
     *
     * @return array
     */
    protected function normalizeRelation(array $relation, array $attributeDefinitions)
    {
        foreach ($attributeDefinitions as $identifier => $attributeDefinition) {
            if (isset($relation['attributes'][$identifier])) {
                continue;
            }

            $relation['attributes'][$identifier] = [
                'value' => $this->repository->toPersistentValue(
                    $this->repository->getEmptyValue($attributeDefinition['type'])
                ),
                'type'  => $attributeDefinition['type'],
            ];
        }

        foreach (array_keys($relation['attributes']) as $identifier) {
            if (!isset($attributeDefinitions[$identifier])) {
                unset($relation['attributes'][$identifier]);
            }
        }

        return $relation;
    }
}
