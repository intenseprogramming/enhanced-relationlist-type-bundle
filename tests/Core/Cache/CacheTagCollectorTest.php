<?php

namespace IntProg\EnhancedRelationListBundle\Core\Cache;

use PHPUnit\Framework\TestCase;

class CacheTagCollectorTest extends TestCase
{

    public function testGetTagsOnContentDelete()
    {
        $collector = new CacheTagCollector();

        $this->assertIsArray(
            $collector->getTagsOnContentDelete(2)
        );
    }

    public function testGetFieldTagsOnContentDelete()
    {
        $collector = new CacheTagCollector();

        $this->assertIsArray(
            $collector->getFieldTagsOnContentDelete(2)
        );
    }

}