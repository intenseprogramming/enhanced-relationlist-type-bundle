<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-21 03:25
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Service\AttributeConverter;

use eZ\Publish\Core\FieldType\ValidationError;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\AbstractValue;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Attribute\TextLine as TextLineValue;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeConverter;

/**
 * Class TextLine.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service\Attribute
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class TextLine extends RelationAttributeConverter
{
    public function fromAbstractValue(AbstractValue $abstractValue)
    {
        return $this->fromHash($abstractValue->value);
    }

    public function toHash(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof TextLineValue) {
            return $attribute->value;
        }

        return null;
    }

    public function fromHash($hash)
    {
        return new TextLineValue(['value' => $hash]);
    }

    public function validate(RelationAttributeBase $attribute, $definition)
    {
        $errors = [];

        if ($attribute instanceof TextLineValue) {
                $min = $definition['settings']['minLength'] ?? null;
                $max = $definition['settings']['maxLength'] ?? null;

                if ($min !== false && $min > mb_strlen($attribute->value)) {
                    $errors[] = new ValidationError('Value must contain at least %min% characters.', null, ['%min%' => $min]);
                } elseif ($max !== null && $max < mb_strlen($attribute->value)) {
                    $errors[] = new ValidationError('Value can only contain up to %max% characters.', null, ['%max%' => $max]);
                }
        }

        return $errors;
    }

    public function isEmpty(RelationAttributeBase $attribute)
    {
        if ($attribute instanceof TextLineValue) {
            return empty($attribute->value);
        }

        return true;
    }
}
