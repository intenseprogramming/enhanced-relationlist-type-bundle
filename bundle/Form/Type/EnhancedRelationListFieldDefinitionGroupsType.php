<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-07-09 18:34
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Form\Type;

use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldDefinitionGroupsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class EnhancedRelationListFieldDefinitionGroupsType.
 *
 * @package   IntProg\EnhancedRelationListBundle\Form\Type
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class EnhancedRelationListFieldDefinitionGroupsType extends AbstractType
{
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return 'intprogenhancedrelationlist_definition_groups';
    }

    /**
     * Adds the model transformer.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new FieldDefinitionGroupsTransformer()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['array_data'] = json_decode($form->getNormData(), true);
    }
}
