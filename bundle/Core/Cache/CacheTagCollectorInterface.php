<?php

namespace IntProg\EnhancedRelationListBundle\Core\Cache;

/**
 * Interface for custom cache tag collectors.
 *
 * @package   IntProg\EnhancedRelationListBundle\Core\Cache
 * @author    Keller, David <daavidkllr@outlook.de>
 * @copyright 2018 Intense Programming
 */
interface CacheTagCollectorInterface
{

    /**
     * @see \IntProg\EnhancedRelationListBundle\Slots\OnDeleteContentSlot
     *
     * @param integer $contentId ContentId of the element which got deleted
     *
     * @return array
     */
    public function getTagsOnContentDelete(int $contentId) : array;

    /**
     * @see \IntProg\EnhancedRelationListBundle\Slots\OnDeleteContentSlot
     *
     * @param integer $contentId ContentId of the element which got deleted
     *
     * @return array
     */
    public function getFieldTagsOnContentDelete(int $contentId) : array;

}