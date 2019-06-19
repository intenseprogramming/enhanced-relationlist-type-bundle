<?php

namespace IntProg\EnhancedRelationListBundle\Slots;

use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use IntProg\EnhancedRelationListBundle\Core\Cache\CacheTagCollector;
use PDOStatement;
use eZ\Publish\API\Repository\Values\Content\Trash\TrashItemDeleteResult;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal;
use eZ\Publish\Core\SignalSlot\Signal\TrashService\EmptyTrashSignal;
use eZ\Publish\Core\SignalSlot\Signal\TrashService\DeleteTrashItemSignal;
use eZ\Publish\Core\Persistence\Cache\Adapter\InMemoryClearingProxyAdapter;
use IntProg\EnhancedRelationListBundle\Service\CacheTagRepository;
use PHPUnit\Framework\TestCase;

class DeleteContentSlotTest extends TestCase
{
    public function testReceive()
    {
        error_reporting(E_ALL);

        $connection         = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $cacheProxyAdapter  = $this->getMockBuilder(InMemoryClearingProxyAdapter::class)->disableOriginalConstructor()->getMock();
        $cacheTagRepository = $this->getMockBuilder(CacheTagRepository::class)->disableOriginalConstructor()->getMock();

        $cacheTagRepository->method('getCollectors')->will($this->returnValue(
            [
                new CacheTagCollector(),
                new CacheTagCollector(),
            ]
        ));

        $statementMock = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $statementMock->method('execute')->willReturn(true);
        $statementMock->expects($this->at(1))->method('fetch')->withAnyParameters()->will($this->returnValue(
            [
                'version'                  => 123,
                'data_text'                => '<?xml version="1.0" encoding="utf-8"?><relation-list><relations><relation content-id="123"><attribute identifier="submenu_element_limit" type="integer">0</attribute></relation></relations></relation-list>',
                'id'                       => 123,
            ]
        ));
        $statementMock->expects($this->at(2))->method('fetch')->withAnyParameters()->will($this->returnValue([]));
        $connection->method('prepare')->willReturn($statementMock);

        $slot = new DeleteContentSlot($connection, $cacheProxyAdapter, $cacheTagRepository);

        $slot->receive(
            new DeleteContentSignal(
                [
                    'contentId' => 123,
                    'affectedLocationIds' => [45, 55]
                ]
            )
        );

        $signal = new EmptyTrashSignal();
        $signal->trashItemDeleteResultList = new TrashItemDeleteResultList(
            [
                'items' => [
                    new TrashItemDeleteResult(['contentId' => 123])
                ]
            ]
        );
        $slot->receive($signal);

        $slot->receive(
            new DeleteTrashItemSignal(
                [
                    'trashItemDeleteResult' => new TrashItemDeleteResult(
                        ['contentId' => 123]
                    )
                ]
            )
        );
    }

}