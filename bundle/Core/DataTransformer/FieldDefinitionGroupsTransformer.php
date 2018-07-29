<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-29 16:21
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class FieldDefinitionGroupsTransformer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\DataTransformer
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class FieldDefinitionGroupsTransformer implements DataTransformerInterface
{
    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     */
    public function transform($value)
    {
        return json_encode($value);
    }

    /**
     * Transforms a value from the transformed representation to its original representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     */
    public function reverseTransform($value)
    {
        return json_decode($value, true);
    }
}
