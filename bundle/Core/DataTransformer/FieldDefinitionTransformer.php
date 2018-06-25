<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-19 19:35
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class FieldDefinitionTransformer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\DataTransformer
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class FieldDefinitionTransformer implements DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function transform($value)
    {
        return [];
    }

    /**
     * Transforms a value from the transformed representation to its original representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException when the transformation fails
     */
    public function reverseTransform($value)
    {
        return '[]';
    }
}
