<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       20/11/2017 22:23
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2017, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle;

use IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IntProgEnhancedRelationListBundle.
 *
 * @package   IntProg\EnhancedRelationListBundle
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2017 Intense Programming
 * @date      20/11/2017 22:23
 */
class IntProgEnhancedRelationListBundle extends Bundle
{
    /**
     * Builds the kernel and adds the compiler passes for collections.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\RelationAttributePass());
        $container->addCompilerPass(new Compiler\CacheTagCollectorPass());
    }
}
