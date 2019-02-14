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

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use Throwable;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RelationAttributeExtension.
 *
 * @package   IntProg\EnhancedRelationListBundle\Templating\Twig\Extension
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class RelationAttributeExtension extends AbstractExtension
{
    /** @var Environment */
    protected $attributeBlockRenderer;

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'erl_render_attribute',
                function (Environment $environment, Field $field, RelationAttributeBase $attribute, $attributeDefinition, array $params = []) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttribute($field, $attribute, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_attribute_input',
                function (Environment $environment, Field $field, $attributeDefinition, $value, $params = []) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttributeInput($field, $value, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_attribute_definition',
                function (Environment $environment, FieldDefinition $fieldDefinition, $attributeDefinition, array $params = []) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttributeDefinition($fieldDefinition, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_attribute_definition_input',
                function (Environment $environment, FieldDefinition $fieldDefinition, $attributeDefinition, $params = []) {
                    $this->attributeBlockRenderer = $environment;

                    return $this->renderAttributeDefinitionInput($fieldDefinition, $attributeDefinition, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Renders the attribute.
     *
     * @param Field                 $field
     * @param RelationAttributeBase $attribute
     * @param array                 $attributeDefinition
     * @param array                 $params
     *
     * @return string
     *
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttribute(Field $field, RelationAttributeBase $attribute, array $attributeDefinition, $params)
    {
        return $this->attributeBlockRenderer->load('IntProgEnhancedRelationListBundle::erl_attributes.html.twig')->renderBlock(
            $attributeDefinition['type'] . '_relation_attribute',
            [
                'attribute'  => $attribute,
                'definition' => $attributeDefinition,
                'field'      => $field,
                'parameters' => $params,
            ]
        );
    }

    /**
     * Renders the input field for the attribute.
     *
     * @param Field $field
     * @param array $value
     * @param array $attributeDefinition
     * @param array $params
     *
     * @return string
     *
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttributeInput(Field $field, $value, array $attributeDefinition, $params)
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
                'field'      => $field,
                'parameters' => $params,
                'hasErrors'  => !empty($errors),
            ]
        );
    }

    /**
     * Renders the attribute definition.
     *
     * @param FieldDefinition $fieldDefinition
     * @param array           $attributeDefinition
     * @param array           $params
     *
     * @return string
     *
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttributeDefinition(FieldDefinition $fieldDefinition, $attributeDefinition, $params)
    {
        return $this->attributeBlockRenderer
            ->load('IntProgEnhancedRelationListBundle::erl_attributes_definition.html.twig')
            ->renderBlock(
                $attributeDefinition['type'] . '_relation_attribute_definition',
                [
                    'definition'      => $attributeDefinition,
                    'fieldDefinition' => $fieldDefinition,
                    'parameters'      => $params,
                ]
            );
    }

    /**
     * Renders the input fields for an attribute definition.
     *
     * @param FieldDefinition $fieldDefinition
     * @param array           $attributeDefinition
     * @param array           $params
     *
     * @return string
     *
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttributeDefinitionInput(FieldDefinition $fieldDefinition, $attributeDefinition, $params)
    {
        return $this->attributeBlockRenderer
            ->load('IntProgEnhancedRelationListBundle::erl_attributes_definition_edit.html.twig')
            ->renderBlock(
                $attributeDefinition['type'] . '_relation_attribute_definition_edit',
                [
                    'definition'      => $attributeDefinition,
                    'fieldDefinition' => $fieldDefinition,
                    'parameters'      => $params,
                ]
            );
    }
}
