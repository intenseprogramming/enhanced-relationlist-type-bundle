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
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\FieldTypeService;
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

    /** @var FieldTypeService $fieldTypeService */
    private $fieldTypeService;

    /**
     * EnhancedRelationListType constructor.
     *
     * @param FieldTypeService            $fieldTypeService
     * @param ContentService              $contentService
     * @param ContentTypeService          $contentTypeService
     * @param RelationAttributeRepository $transformer
     */
    public function __construct(
        FieldTypeService $fieldTypeService,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        RelationAttributeRepository $transformer
    )
    {
        $this->fieldTypeService   = $fieldTypeService;
        $this->contentService     = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->transformer        = $transformer;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return TextType::class;
    }

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
     *
     * @throws NotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(
            new FieldValueTransformer(
                $this->fieldTypeService->getFieldType('intprogenhancedrelationlist'),
                $this->transformer,
                $options['sub_attributes']
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $allowedContentTypeIdentifiers = [];
        $allowedContentTypeIds         = $options['allowed_content_type_ids'];
        foreach ($allowedContentTypeIds as $contentTypeId) {
            try {
                $allowedContentTypeIdentifiers[] =
                    $this->contentTypeService->loadContentType($contentTypeId)->identifier;
            } catch (NotFoundException $exception) {
                continue;
            }
        }

        $view->vars['limit']                            = $options['selection_limit'];
        $view->vars['default_location']                 = $options['browse_location'];
        $view->vars['allowed_content_type_identifiers'] = $allowedContentTypeIdentifiers;

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

        $data            = $form->getData();
        $attributeErrors = [];
        if ($data instanceof Value) {
            $attributeErrors = $data->attributeErrors;
        }

        $view->vars['attribute_errors'] = $attributeErrors;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('browse_location', 1);
        $resolver->setDefault('selection_limit', 0);
        $resolver->setDefault('allowed_content_type_ids', []);
        $resolver->setDefault('sub_attributes', []);
    }
}
