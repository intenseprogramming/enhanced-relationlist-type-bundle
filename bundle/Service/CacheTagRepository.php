<?php

namespace IntProg\EnhancedRelationListBundle\Service;

use IntProg\EnhancedRelationListBundle\Core\Cache\CacheTagCollectorInterface;

/**
 * Cache tag repository.
 *
 * @package   IntProg\EnhancedRelationListBundle\Service
 * @author    Keller, David <daavidkllr@outlook.de>
 * @copyright 2018 Intense Programming
 */
class CacheTagRepository
{
    /**
     * @var array
     */
    protected $collectors;

    /**
     * @param array $collectors
     */
    public function __construct(array $collectors)
    {
        $this->collectors = $collectors;
    }

    /**
     * @return CacheTagCollectorInterface[]
     */
    public function getCollectors() : array
    {
        return $this->collectors;
    }
}
