<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2019-05-10 10:37 PM
 * @author     Konrad, Steve <skonrad@wingmail.net>
 * @copyright  Copyright © 2019, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Templating\Twig;

use IntProg\EnhancedRelationListBundle\Core\Exception\MissingAttributeBlockException;
use IntProg\EnhancedRelationListBundle\Core\RelationAttributeBase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Template;

/**
 * Class AttributeBlockRenderer.
 *
 * @package   IntProg\EnhancedRelationListBundle\Templating\Twig
 * @author    Konrad, Steve <skonrad@wingmail.net>
 * @copyright 2019 Intense Programming
 */
class AttributeBlockRenderer
{
    /** @var Environment $twig */
    protected $twig;

    /** @var array $blocks */
    private $blocks = [];

    /**
     * A \Twig\Template instance used to render template blocks, or path to the template to use.
     *
     * @var Template|string
     */
    private $baseTemplate;

    /**
     * Array of Twig template resources for attribute view.
     *
     * @var Template[]|array
     */
    private $attributeViewResources = [];

    /**
     * Array of Twig template resources for attribute definition view.
     *
     * @var Template[]|array
     */
    private $attributeDefinitionViewResources = [];

    /**
     * Setter for the twig environment.
     *
     * @param Environment $environment The twig environment.
     *
     * @return void
     */
    public function setTwig(Environment $environment)
    {
        $this->twig = $environment;
    }

    /**
     * @param string|Template $baseTemplate
     */
    public function setBaseTemplate($baseTemplate)
    {
        $this->baseTemplate = $baseTemplate;
    }

    /**
     * @param array $attributeViewResources
     */
    public function setAttributeViewResources(array $attributeViewResources)
    {
        $this->attributeViewResources = (array) $attributeViewResources;
        usort($this->attributeViewResources, [$this, 'sortResourcesCallback']);
    }

    /**
     * @param array $attributeDefinitionViewResources
     */
    public function setAttributeDefinitionViewResources(array $attributeDefinitionViewResources)
    {
        $this->attributeDefinitionViewResources = (array) $attributeDefinitionViewResources;
        usort($this->attributeDefinitionViewResources, [$this, 'sortResourcesCallback']);
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public function sortResourcesCallback(array $a, array $b)
    {
        return $b['priority'] - $a['priority'];
    }

    /**
     * @param RelationAttributeBase $attribute
     * @param array                 $attributeDefinition
     * @param array                 $params
     *
     * @return string
     *
     * @throws MissingAttributeBlockException When no template block can be found for $attribute
     * @throws LoaderError                    When the template cannot be found
     * @throws RuntimeError                   When a previously generated cache is corrupted
     * @throws SyntaxError                    When an error occurred during compilation
     */
    public function renderAttributeView(
        RelationAttributeBase $attribute,
        array $attributeDefinition,
        array $params = []
    )
    {
        $localTemplate = null;
        if (isset($params['template'])) {
            // Local override of the template.
            // This template is put on the top the templates stack.
            $localTemplate = $params['template'];
            unset($params['template']);
        }

        $params += [
            'attribute'  => $attribute,
            'definition' => $attributeDefinition,
        ];

        // Getting instance of Twig_Template that will be used to render blocks
        if (is_string($this->baseTemplate)) {
            $this->baseTemplate = $this->twig->loadTemplate($this->baseTemplate);
        }

        $blockName = $this->getRenderAttributeBlockName($attribute->getTypeIdentifier());
        $context   = $this->twig->mergeGlobals($params);
        $blocks    = $this->getBlocksByAttributeIdentifier($attribute->getTypeIdentifier(), $localTemplate);

        if (!$this->baseTemplate->hasBlock($blockName, $context, $blocks)) {
            throw new MissingAttributeBlockException("Cannot find '$blockName' template block.");
        }

        return $this->baseTemplate->renderBlock($blockName, $context, $blocks);
    }

    /**
     * @param string $attributeIdentifier
     * @param array  $attributeDefinition
     * @param array  $params
     *
     * @return string
     *
     * @throws LoaderError  When the template cannot be found
     * @throws RuntimeError When a previously generated cache is corrupted
     * @throws SyntaxError  When an error occurred during compilation
     */
    public function renderAttributeDefinitionView(
        string $attributeIdentifier,
        array $attributeDefinition,
        array $params
    )
    {
        $params += ['definition' => $attributeDefinition];

        // Getting instance of Twig_Template that will be used to render blocks
        if (is_string($this->baseTemplate)) {
            $this->baseTemplate = $this->twig->loadTemplate($this->baseTemplate);
        }

        $blockName = $this->getRenderAttributeDefinitionBlockName($attributeIdentifier);
        $context   = $this->twig->mergeGlobals($params);
        $blocks    = $this->getBlocksByDefinitionIdentifier($attributeIdentifier);

        if (!$this->baseTemplate->hasBlock($blockName, $context, $blocks)) {
            return '';
        }

        return $this->baseTemplate->renderBlock($blockName, $context, $blocks);
    }

    /**
     * @param string   $blockName
     * @param Template $tpl
     *
     * @return array|null (a valid block is of type array).
     *
     * @throws LoaderError
     */
    private function searchBlock(string $blockName, Template $tpl)
    {
        // Current template might have parents, so we need to loop against them to find a matching block
        do {
            foreach ($tpl->getBlocks() as $name => $block) {
                if ($name === $blockName) {
                    return $block;
                }
            }
        } while (($tpl = $tpl->getParent([])) instanceof Template);

        return null;
    }

    /**
     * @param string $fieldTypeIdentifier
     * @param null   $localTemplate
     *
     * @return array
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getBlocksByAttributeIdentifier(string $fieldTypeIdentifier, $localTemplate = null)
    {
        $fieldBlockName = $this->getRenderAttributeBlockName($fieldTypeIdentifier);
        if ($localTemplate !== null) {
            // $localTemplate might be a Twig_Template instance already (e.g. using _self Twig keyword)
            if (!$localTemplate instanceof Template) {
                $localTemplate = $this->twig->loadTemplate($localTemplate);
            }

            $block = $this->searchBlock($fieldBlockName, $localTemplate);
            if ($block !== null) {
                return [$fieldBlockName => $block];
            }
        }

        return $this->getBlockByName($fieldBlockName, 'attributeViewResources');
    }

    /**
     * @param string $name
     * @param string $resourcesName
     *
     * @return array
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getBlockByName(string $name, string $resourcesName)
    {
        if (isset($this->blocks[$name])) {
            return [$name => $this->blocks[$name]];
        }

        foreach ($this->{$resourcesName} as &$template) {
            if (!$template instanceof Template) {
                $template = $this->twig->loadTemplate($template['template']);
            }

            $tpl = $template;

            $block = $this->searchBlock($name, $tpl);
            if ($block !== null) {
                $this->blocks[$name] = $block;

                return [$name => $block];
            }
        }

        return [];
    }

    /**
     * @param string $definitionIdentifier
     *
     * @return array
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    private function getBlocksByDefinitionIdentifier(string $definitionIdentifier)
    {
        return $this->getBlockByName($definitionIdentifier, 'attributeDefinitionViewResources');
    }

    /**
     * @param string $fieldTypeIdentifier
     *
     * @return string
     */
    private function getRenderAttributeBlockName(string $fieldTypeIdentifier)
    {
        return $fieldTypeIdentifier . '_relation_attribute';
    }

    /**
     * @param string $fieldTypeIdentifier
     *
     * @return string
     */
    private function getRenderAttributeDefinitionBlockName(string $fieldTypeIdentifier)
    {
        return $fieldTypeIdentifier . '_relation_attribute_definition';
    }
}
