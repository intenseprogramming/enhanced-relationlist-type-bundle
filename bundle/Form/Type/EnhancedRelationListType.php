<?php
/**
 * @category   PHP
 * @package    intprog
 * @version    1
 * @date       2018-02-17 16:58
 * @author     Konrad, Steve <s.konrad@wingmail.net>
 * @copyright  Copyright © 2018, Intense Programming
 */

namespace IntProg\EnhancedRelationListBundle\Form\Type;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\SPI\FieldType\ValidationError;
use IntProg\EnhancedRelationListBundle\Core\DataTransformer\FieldValueTransformer;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Value;
use IntProg\EnhancedRelationListBundle\Service\RelationAttributeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EnhancedRelationListType.
 *
 * @package   IntProg\EnhancedRelationListBundle\Form\Type
 * @author    Konrad, Steve <s.konrad@wingmail.net>
 * @copyright 2018 Intense Programming
 */
class EnhancedRelationListType extends AbstractType
{
    /** @var ContentService $contentService */
    protected $contentService;

    /** @var ContentTypeService $contentTypeService */
    protected $contentTypeService;

    /** @var RelationAttributeRepository $transformer */
    protected $transformer;

    /**
     * EnhancedRelationListType constructor.
     *
     * @param ContentService              $contentService
     * @param RelationAttributeRepository $transformer
     */
    public function __construct(
        ContentService $contentService,
        RelationAttributeRepository $transformer
    )
    {
        $this->contentService = $contentService;
        $this->transformer    = $transformer;
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
        return TextType::class;
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'intprogenhancedrelationlist';
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
            new FieldValueTransformer(
                $this->transformer,
                $options['sub_attributes']
            )
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
        $view->vars['limit']                            = $options['selection_limit'];
        $view->vars['default_location']                 = $options['browse_location'];
        $view->vars['allowed_content_type_identifiers'] = $options['allowed_content_type_identifiers'];

        $view->vars['array_data'] = array_map(
            function ($relation) {
                if (!isset($relation['contentId'])) {
                    return $relation;
                }

                $relation['vars'] = [
                    'content' => $this->contentService->loadContent($relation['contentId']),
                ];

                return $relation;
            },
            json_decode($form->getNormData(), true)
        );

        $data       = $form->getData();
        $rowsErrors = [];
        if ($data instanceof Value) {
            foreach ($data->attributeErrors as $index => $attributeErrors) {
                $rowErrors = [];
                foreach ($attributeErrors as $attributeIdentifier => $attributeError) {
                    foreach ($attributeError as $error) {
                        if ($error instanceof ValidationError) {
                            $rowErrors[$attributeIdentifier][] = (string) $error->getTranslatableMessage();
                        }
                    }
                }
                $rowsErrors[$index] = $rowErrors;
            }
        }

        $view->vars['attribute_errors'] = $rowsErrors;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('browse_location', 1);
        $resolver->setDefault('selection_limit', 0);
        $resolver->setDefault('allowed_content_type_identifiers', []);
        $resolver->setDefault('sub_attributes', []);
    }
}
