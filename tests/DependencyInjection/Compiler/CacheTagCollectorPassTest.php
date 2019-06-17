<?php

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CacheTagCollectorPassTest extends TestCase
{
    public function testProcess()
    {
        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('IntProg\EnhancedRelationListBundle\Service\CacheTagRepository')
            ->willReturn(false);

        $compilerPass = new CacheTagCollectorPass();
        $compilerPass->process($container);
    }

    public function testMissingRepositoryDefinition()
    {
        $serviceTags = [
            'service.1' => [],
            'service.2' => [],
        ];

        $repositoryDefinition = $this->createMock(Definition::class);
        $repositoryDefinition
            ->expects($this->once())
            ->method('setArguments')
            ->with([
                [
                    new Reference('service.1'),
                    new Reference('service.2'),
                ]
            ]);

        $container = $this->createMock(ContainerBuilder::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('IntProg\EnhancedRelationListBundle\Service\CacheTagRepository')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with('int.prog.erl.cache.tag_collector')
            ->willReturn($serviceTags);
        $container
            ->expects($this->once())
            ->method('findDefinition')
            ->with('IntProg\EnhancedRelationListBundle\Service\CacheTagRepository')
            ->willReturn($repositoryDefinition);

        $compilerPass = new CacheTagCollectorPass();
        $compilerPass->process($container);
    }
}
