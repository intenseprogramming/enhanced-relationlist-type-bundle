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

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\LanguageService;
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
    protected $languageService;

    protected $languages;

    public function __construct(LanguageService $languageService, array $languages)
    {
        $this->languageService = $languageService;
        $this->languages       = $languages;
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
     * Finishes the form view.
     *
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     *
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {

        $languageMap = [];
        foreach ($this->languages as $languageCode) {
            try {
                $language = $this->languageService->loadLanguage($languageCode);
            } catch (NotFoundException $exception) {
                continue;
            }

            $languageMap[] = [
                'code' => $language->languageCode,
                'name' => $language->name,
            ];
        }

        $view->vars['languageMap']             = $languageMap;
        $view->vars['array_data'] = json_decode($form->getNormData(), true);
    }
}
