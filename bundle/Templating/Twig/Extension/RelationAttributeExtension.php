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

use Doctrine\DBAL\Driver\Connection;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use IntProg\EnhancedRelationListBundle\Core\Exception\MissingAttributeBlockException;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value\Relation;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use IntProg\EnhancedRelationListBundle\Templating\Twig\AttributeBlockRenderer;
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
    /** @var AttributeBlockRenderer $attributeBlockRenderer */
    protected $attributeBlockRenderer;

    /**
     * @var Connection $connection
     * @deprecated
     */
    protected $connection;

    /**
     * @var ContentService $contentService
     * @deprecated
     */
    protected $contentService;

    /**
     * RelationAttributeExtension constructor.
     *
     * @param AttributeBlockRenderer $attributeBlockRenderer
     * @param Connection             $connection
     * @param ContentService         $contentService
     */
    public function __construct(AttributeBlockRenderer $attributeBlockRenderer, Connection $connection, ContentService $contentService)
    {
        $this->attributeBlockRenderer = $attributeBlockRenderer;
        $this->connection             = $connection;
        $this->contentService         = $contentService;
    }

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
                    user_error('Template function erl_render_attribute is deprecated and will be removed in v2.0. Use erl_render_relation_attribute instead.', E_USER_DEPRECATED);
                    $this->attributeBlockRenderer->setTwig($environment);

                    list($content, $attributeIdentifier, $relation) = $this->getAttributeRenderInformation($field, $attribute);

                    return $this->renderAttributeView($content, $field, $relation, $attributeIdentifier, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_relation_attribute',
                function (Environment $environment, Content $content, Field $field, Relation $relation, $attributeIdentifier, array $params = []) {
                    $this->attributeBlockRenderer->setTwig($environment);

                    return $this->renderAttributeView($content, $field, $relation, $attributeIdentifier, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_attribute_definition',
                function (
                    Environment $environment,
                    FieldDefinition $fieldDefinition,
                    $attributeDefinition,
                    array $params = []
                ) {
                    user_error('Template function erl_render_attribute_definition is deprecated and will be removed in v2.0. Use erl_render_relation_attribute_definition instead.', E_USER_DEPRECATED);
                    $this->attributeBlockRenderer->setTwig($environment);

                    list($attributeIdentifier) = $this->getDefinitionRenderInformation($fieldDefinition, $attributeDefinition);

                    return $this->renderAttributeDefinition($fieldDefinition, $attributeIdentifier, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'erl_render_relation_attribute_definition',
                function (
                    Environment $environment,
                    FieldDefinition $fieldDefinition,
                    $attributeIdentifier,
                    array $params = []
                ) {
                    $this->attributeBlockRenderer->setTwig($environment);

                    return $this->renderAttributeDefinition($fieldDefinition, $attributeIdentifier, $params);
                },
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Renders the attribute.
     *
     * @param Content  $content
     * @param Field    $field
     * @param Relation $relation
     * @param string   $attributeIdentifier
     * @param array    $params
     *
     * @return string
     *
     * @throws MissingAttributeBlockException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttributeView(Content $content, Field $field, Relation $relation, $attributeIdentifier, $params)
    {
        $fieldSettings = $content->getContentType()->getFieldDefinition($field->fieldDefIdentifier)->fieldSettings;

        $params = $params + ['content' => $content, 'field' => $field];

        return $this->attributeBlockRenderer->renderAttributeView(
            $relation->attributes[$attributeIdentifier],
            $fieldSettings['attributeDefinitions'][$attributeIdentifier],
            $params
        );
    }

    /**
     * Renders the attribute definition.
     *
     * @param FieldDefinition $fieldDefinition
     * @param string           $attributeIdentifier
     * @param array           $params
     *
     * @return string
     *
     * @throws Throwable
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderAttributeDefinition(FieldDefinition $fieldDefinition, $attributeIdentifier, $params)
    {
        $fieldSettings = $fieldDefinition->fieldSettings;

        $params = $params + ['fieldDefinition' => $fieldDefinition];

        return $this->attributeBlockRenderer->renderAttributeDefinitionView(
            $attributeIdentifier,
            $fieldSettings['attributeDefinitions'][$attributeIdentifier],
            $params
        );
    }

    private function getAttributeRenderInformation(Field $field, RelationAttributeBase $attribute)
    {
        $stmt    = $this->connection->prepare('SELECT contentobject_id FROM ezcontentobject_attribute WHERE id = ' . $field->id);
        $stmt->execute();

        $content = $this->contentService->loadContent($stmt->fetchColumn());

        /** @var Value $fieldValue */
        $fieldValue = $field->value;

        foreach ($fieldValue->relations as $relation) {
            if (in_array($attribute, $relation->attributes, true)) {
                return [$content, array_search($attribute, $relation->attributes, true), $relation];
            }
        }
        foreach ($fieldValue->groups as $group) {
            foreach ($group->relations as $relation) {
                if (in_array($attribute, $relation->attributes, true)) {
                    return [$content, array_search($attribute, $relation->attributes, true), $relation];
                }
            }
        }

        return [$content, null, null];
    }

    private function getDefinitionRenderInformation(FieldDefinition $fieldDefinition, $attributeDefinition)
    {
        $fieldSettings = $fieldDefinition->fieldSettings;

        return [array_search($attributeDefinition, $fieldSettings['attributeDefinitions'])];
    }
}
