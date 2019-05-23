<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 09:56
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class IntProgEnhancedRelationListExtensionTest extends TestCase
{
    public function testPrepend()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->exactly(4))->method('prependExtensionConfig');
        $container->expects($this->exactly(3))->method('addResource');

        $extension = new IntProgEnhancedRelationListExtension();
        $extension->prepend($container);
    }

    public function testLoad()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->exactly(5))->method('fileExists');
        $container->expects($this->never())->method('setParameter');

        $extension = new IntProgEnhancedRelationListExtension();
        $extension->load([], $container);
    }
}
