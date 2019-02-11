<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-19 19:30
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Form\Type;

use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldDefinitionAttributesTransformer;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class EnhancedRelationListFieldDefinitionAttributesType.
 *
 * @package   IntProg\EnhancedRelationListBundle\Form\Type
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class EnhancedRelationListFieldDefinitionAttributesType extends AbstractType
{
    /** @var RelationAttributeRepository $attributeRepository */
    protected $attributeRepository;

    /**
     * EnhancedRelationListFieldDefinitionAttributesType constructor.
     *
     * @param RelationAttributeRepository $attributeRepository
     */
    public function __construct(RelationAttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Returns the name of the form type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * Returns the name of the parent type.
     *
     * @return string
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'intprogenhancedrelationlist_definition_attributes';
    }

    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new FieldDefinitionAttributesTransformer()
        );
    }

    /**
     * Builds the form view.
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     *
     * @return void
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['availableAttributeTypes'] = array_keys($this->attributeRepository->getConverters());
        $view->vars['attributesData']          = $form->getData();
    }
}
