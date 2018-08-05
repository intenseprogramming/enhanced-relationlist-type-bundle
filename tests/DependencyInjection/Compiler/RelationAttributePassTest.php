<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-08-05 09:31
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RelationAttributePassTest extends TestCase
{
    public function testProcess()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository')
            ->willReturn(false);

        $compilerPass = new RelationAttributePass();
        $compilerPass->process($container);
    }

    public function testMissingRepositoryDefinition()
    {
        $serviceTags = [
            'service.1' => [['identifier' => 'service_1']],
            'service.2' => [['identifier' => 'service_2']],
        ];

        $repositoryDefinition = $this->createMock(Definition::class);
        $repositoryDefinition
            ->expects($this->once())
            ->method('setArguments')
            ->with([
                [
                    'service_1' => new Reference('service.1'),
                    'service_2' => new Reference('service.2'),
                ]
            ]);

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('int.prog.erl.relation.attribute')
            ->willReturn($serviceTags);
        $container
            ->expects($this->once())
            ->method('findDefinition')
            ->with('IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository')
            ->willReturn($repositoryDefinition);

        $compilerPass = new RelationAttributePass();
        $compilerPass->process($container);
    }
}
