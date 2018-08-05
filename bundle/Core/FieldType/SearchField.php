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

use eZ\Publish\SPI\FieldType\Indexable;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Search;

/**
 * Class SearchField.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\FieldType
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class SearchField implements Indexable
{
    /**
     * Get index data for field for search backend.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field                $field
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \eZ\Publish\SPI\Search\Field[]
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        return array(
            new Search\Field(
                'value',
                [], // TODO: generate array of relation ids.
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'sort_value',
                implode('-', []), // TODO: generate array of relation ids.
                new Search\FieldType\StringField()
            ),
        );
    }

    /**
     * Get index field types for search backend.
     *
     * @return \eZ\Publish\SPI\Search\FieldType[]
     */
    public function getIndexDefinition()
    {
        return array(
            'value' => new Search\FieldType\MultipleStringField(),
            'sort_value' => new Search\FieldType\StringField(),
        );
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'value';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return 'sort_value';
    }
}
