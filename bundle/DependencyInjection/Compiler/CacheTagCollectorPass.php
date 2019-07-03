<?php

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Cache tag collector pass.
 *
 * @package   IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler
 * @author    Keller, David <daavidkllr@outlook.de>
 * @copyright 2018 Intense Programming
 */
class CacheTagCollectorPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('IntProg\EnhancedRelationListBundle\Service\CacheTagRepository')) {
            return;
        }

        $servicesTagged = $container->findTaggedServiceIds('int.prog.erl.cache.tag_collector');
        $services       = [];

        foreach ($servicesTagged as $id => $tags) {
            $services[] = new Reference($id);
        }

        $definition = $container->findDefinition('IntProg\EnhancedRelationListBundle\Service\CacheTagRepository');
        $definition->setArguments([$services]);
    }
}
