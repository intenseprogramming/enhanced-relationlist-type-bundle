<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-06-11 21:37
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Templating\Twig\Extension;

use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Class RelationAttributeExtension.
 *
 * @package   IntProg\EnhancedRelationListBundle\Templating\Twig\Extension
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class RelationAttributeExtension extends Twig_Extension
{
    /** @var Twig_Environment */
    protected $attributeBlockRenderer;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'erl_render_attribute',
                function (Twig_Environment $environment, RelationAttributeBase $attribute, $attributeDefinition, array $params = []) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttribute($attribute, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new Twig_SimpleFunction(
                'erl_render_attribute_input',
                function (Twig_Environment $environment, $attributeDefinition, $value, $params) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttributeInput($value, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * TODO: Insert description for renderAttribute.
     *
     * @param RelationAttributeBase $attribute
     * @param array                 $attributeDefinition
     * @param                       $params
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderAttribute(RelationAttributeBase $attribute, array $attributeDefinition, $params)
    {
        return $this->attributeBlockRenderer->load('IntProgEnhancedRelationListBundle::erl_attributes.html.twig')->renderBlock(
            $attributeDefinition['type'] . '_relation_attribute',
            [
                'attribute'  => $attribute,
                'definition' => $attributeDefinition,
                'parameters' => $params,
            ]
        );
    }

    /**
     * TODO: Insert description for renderAttributeInput.
     *
     * @param       $value
     * @param array $attributeDefinition
     * @param       $params
     *
     * @return string
     *
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderAttributeInput($value, array $attributeDefinition, $params)
    {
        $errors = [];
        if (isset($params['errors'])) {
            $errors = $params['errors'];
            unset($params['errors']);
        }

        return $this->attributeBlockRenderer->load('IntProgEnhancedRelationListBundle::erl_attributes_edit.html.twig')->renderBlock(
            $attributeDefinition['type'] . '_relation_attribute_edit',
            [
                'value'      => $value,
                'definition' => $attributeDefinition,
                'parameters' => $params,
                'hasErrors'  => !empty($errors),
            ]
        );
    }
}
