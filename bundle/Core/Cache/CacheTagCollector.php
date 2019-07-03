<?php

namespace IntProg\EnhancedRelationListBundle\Core\Cache;

/**
 * Default cache tag collector for delete ERL cache tags.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\Cache
 * @author    Keller, David <daavidkllr@outlook.de>
 * @copyright 2018 Intense Programming
 */
class CacheTagCollector implements CacheTagCollectorInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTagsOnContentDelete(int $contentId) : array
    {
        return ['content-' . $contentId];
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldTagsOnContentDelete(int $fieldId) : array
    {
        return ['content-fields-' . $fieldId];
    }

}