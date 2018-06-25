<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 17:25
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Core;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Class RelationAttributeBase.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\Interfaces
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
abstract class RelationAttributeBase extends ValueObject
{
    /**
     * Returns the type identifier of the relation attribute.
     *
     * @return string
     */
    abstract public function getTypeIdentifier();
}
