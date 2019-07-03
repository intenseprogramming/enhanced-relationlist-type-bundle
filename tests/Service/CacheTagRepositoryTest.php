<?php

namespace IntProg\EnhancedRelationListBundle\Service;

use IntProg\EnhancedRelationListBundle\Core\Cache\CacheTagCollectorInterface;
use PHPUnit\Framework\TestCase;

class CacheTagRepositoryTest extends TestCase
{

    public function testGetCollectors()
    {
        $collectors = [
            $this->createMock(CacheTagCollectorInterface::class),
            $this->createMock(CacheTagCollectorInterface::class),
        ];

        $repository = new CacheTagRepository($collectors);

        $this->assertIsArray(
            $repository->getCollectors()
        );

        foreach ($repository->getCollectors() as $collector) {
            $this->assertInstanceOf(
                CacheTagCollectorInterface::class,
                $collector
            );
        }
    }

}
