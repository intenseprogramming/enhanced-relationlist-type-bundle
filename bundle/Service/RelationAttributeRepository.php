<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-11 20:26
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service;

use eZ\Publish\Core\FieldType\ValidationError;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class RelationAttributeRepository.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class RelationAttributeRepository
{
    /** @var array|RelationAttributeConverter[] $converters */
    protected $converters = [];

    /**
     * RelationAttributeRepository constructor.
     *
     * @param array|RelationAttributeConverter[] $converters
     */
    public function __construct(array $converters)
    {
        $this->converters = $converters;
    }

    public function getConverters()
    {
        return $this->converters;
    }

    public function convertAbstractValue(AbstractValue $abstractValue, $targetType)
    {
        return $this->converters[$targetType]->fromAbstractValue($abstractValue);
    }

    public function fromPersistentValue($value, $targetType)
    {
        return $this->converters[$targetType]->fromHash($value);
    }

    public function toPersistentValue(RelationAttributeBase $attribute)
    {
        return $this->converters[$attribute->getTypeIdentifier()]->toHash($attribute);
    }

    public function validate(RelationAttributeBase $attribute, $identifier, $definition)
    {
        if ($definition['required'] ?? false) {
            if ($this->converters[$attribute->getTypeIdentifier()]->isEmpty($attribute)) {
                return [
                    new ValidationError(
                        'The attribute "%identifier%" is required.',
                        null,
                        [
                            '%identifier%' => $identifier
                        ]
                    ),
                ];
            }
        }

        return $this->converters[$attribute->getTypeIdentifier()]->validate($attribute, $definition);
    }
}
