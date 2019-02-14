<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-18 01:56
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * Class IntProgEnhancedRelationListExtension.
 *
 * @package   IntProg\EnhancedRelationListBundle\DependencyInjection
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class IntProgEnhancedRelationListExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads and sets services and configs.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('fieldtypes.yml');
        $loader->load('form.yml');
        $loader->load('relation_attributes.yml');
    }

    /**
     * Adds system configuration to the config load chain.
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function prepend(ContainerBuilder $container)
    {
        $configsDir = __DIR__ . '/../Resources/config/';
        $configs    = [
            'twig.yml'               => 'twig',
            'ezdesign.yml'               => 'ezdesign',
            'ez_field_templates.yml' => 'ezpublish',
        ];

        foreach ($configs as $file => $namespace) {
            $config = Yaml::parse(file_get_contents($configsDir . $file));
            $container->prependExtensionConfig($namespace, $config);
            $container->addResource(new FileResource($configsDir . $file));
        }

        $container->prependExtensionConfig(
            'bazinga_js_translation',
            [
                'active_domains' => [
                    'enhancedrelationlist',
                    'enhancedrelationlist_definition_attribute',
                    'enhancedrelationlist_definition_group',
                ]
            ]
        );
    }
}
