<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 01:45
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle;

use IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IntProgEnhancedRelationListBundleTest extends TestCase
{
    public function testBuild()
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder->expects($this->exactly(2))->method('addCompilerPass')->with(
            $this->logicalOr(
                new Compiler\RelationAttributePass(),
                new Compiler\CacheTagCollectorPass()
            )
        );

        $bundle = new IntProgEnhancedRelationListBundle();

        $bundle->build($containerBuilder);
    }
}