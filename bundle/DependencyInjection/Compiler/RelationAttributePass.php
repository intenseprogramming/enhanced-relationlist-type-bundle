<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-21 03:45
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RelationAttributePass.
 *
 * @package   IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class RelationAttributePass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository')) {
            return;
        }

        $servicesTagged = $container->findTaggedServiceIds('int.prog.erl.relation.attribute');
        $services       = [];

        foreach ($servicesTagged as $id => $tags) {
            foreach ($tags as $attributes) {
                $services[$attributes['identifier']] = new Reference($id);
            }
        }

        $definition = $container->findDefinition(
            'IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository'
        );
        $definition->setArguments([$services]);
    }
}
