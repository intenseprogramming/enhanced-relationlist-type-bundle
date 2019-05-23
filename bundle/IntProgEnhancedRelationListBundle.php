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

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension;
use IntProg\EnhancedRelationListBundle\DependencyInjection\Compiler;
use IntProg\EnhancedRelationListBundle\DependencyInjection\ConfigResolver\EnhancedRelationListConfigParser;
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

        /** @var EzPublishCoreExtension $eZExtension */
        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addConfigParser(
            new EnhancedRelationListConfigParser()
        );
    }
}
