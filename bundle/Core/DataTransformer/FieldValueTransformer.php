<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-19 22:53
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\DataTransformer;

use eZ\Publish\API\Repository\FieldType;
use EzSystems\RepositoryForms\FieldType\DataTransformer\FieldValueTransformer as EzFieldValueTransformer;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeTransformer;

/**
 * Class FieldValueTransformer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\DataTransformer
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class FieldValueTransformer extends EzFieldValueTransformer
{
    protected $transformer;

    protected $configuration;

    public function __construct(FieldType $fieldType, RelationAttributeTransformer $transformer, array $configuration)
    {
        parent::__construct($fieldType);

        $this->transformer   = $transformer;
        $this->configuration = $configuration;
    }

    /**
     * Transforms a FieldType Value into a hash using `FieldTpe::toHash()`.
     *
     * @param mixed $value
     *
     * @return array|null the value's hash, or null if $value was not a FieldType Value
     */
    public function transform($value)
    {
        if (!$value instanceof Value) {
            return null;
        }

        $result = [];
        foreach ($value->relations as $index => $relation) {
            $result[$index] = [
                'contentId'  => $relation->contentId,
                'attributes' => [],
            ];

            foreach ($relation->attributes as $identifier => $attribute) {
                $result[$index]['attributes'][$identifier] = $this->transformer->toPersistentValue($attribute);
            }
        }

        return json_encode($result);
    }

    /**
     * Transforms a hash into a FieldType Value using `FieldType::fromHash()`.
     *
     * @param mixed $value
     *
     * @return \eZ\Publish\SPI\FieldType\Value
     */
    public function reverseTransform($value)
    {
        if (empty($value) || !is_string($value ?? null)) {
            return new Value();
        }

        $value = json_decode($value, true);

        $relations = [];
        foreach ($value as $relation) {
            foreach ($relation['attributes'] as $identifier => $attribute) {
                $relation['attributes'][$identifier] = $this->transformer->fromPersistentValue(
                    $attribute,
                    $this->configuration[$identifier]['type']
                );
            }

            $relations[] = new Value\Relation($relation);
        }

        return new Value($relations);
    }
}
