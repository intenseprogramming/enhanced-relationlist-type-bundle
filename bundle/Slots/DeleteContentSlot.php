<?php

namespace IntProg\EnhancedRelationListBundle\Slots;

use Doctrine\DBAL\Connection;
use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;
use eZ\Publish\Core\SignalSlot\Signal\ContentService\DeleteContentSignal;
use eZ\Publish\Core\SignalSlot\Signal\TrashService\EmptyTrashSignal;
use eZ\Publish\Core\SignalSlot\Signal\TrashService\DeleteTrashItemSignal;
use eZ\Publish\Core\Persistence\Cache\Adapter\InMemoryClearingProxyAdapter;
use IntProg\EnhancedRelationListBundle\Core\FieldType\Type;
use IntProg\EnhancedRelationListBundle\Service\CacheTagRepository;
use PDO;
use DOMDocument;
use DOMXPath;

/**
 * Delete content slot.
 *
 * @package   IntProg\EnhancedRelationListBundle\Slots
 * @author    Keller, David <daavidkllr@outlook.de>
 * @copyright 2018 Intense Programming
 */
class DeleteContentSlot extends Slot
{

    /**
     * @var Connection
     */
    protected $databaseConnection;

    /**
     * @var InMemoryClearingProxyAdapter
     */
    protected $cacheProxyAdapter;

    /**
     * @var CacheTagRepository
     */
    protected $cacheTagRepository;

    /**
     * @param Connection                   $databaseConnection
     * @param InMemoryClearingProxyAdapter $cacheProxyAdapter
     * @param CacheTagRepository           $cacheTagRepository
     */
    public function __construct(
        Connection $databaseConnection,
        InMemoryClearingProxyAdapter $cacheProxyAdapter,
        CacheTagRepository $cacheTagRepository
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->cacheProxyAdapter  = $cacheProxyAdapter;
        $this->cacheTagRepository = $cacheTagRepository;
    }

    /**
     * @param Signal $signal
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function receive(Signal $signal)
    {
        $contentIdsList = [];

        if ($signal instanceof DeleteTrashItemSignal) {
            $contentIdsList[] = $signal->trashItemDeleteResult->contentId;

        } elseif ($signal instanceof EmptyTrashSignal) {
            foreach ($signal->trashItemDeleteResultList->items as $item) {
                $contentIdsList[] = $item->contentId;
            }
        } elseif ($signal instanceof DeleteContentSignal) {
            $contentIdsList[] = $signal->contentId;
        }

        foreach ($contentIdsList as $contentId) {
            $statement = $this->databaseConnection->prepare(
                sprintf(
                    "SELECT * FROM ezcontentobject_attribute WHERE data_type_string = '%s' and data_text like '%%content-id=\"%s\"%%'",
                    Type::FIELD_TYPE_IDENTIFIER,
                    $contentId
                )
            );
            $statement->execute();

            $cacheTags = [];

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $document = new DOMDocument('1.0', 'utf-8');
                $document->loadXML($row['data_text']);

                $xpath = new DOMXPath($document);
                $xpathExpression = "//relation-list/relations/relation[@content-id='{$contentId}']";

                $relationItems = $xpath->query($xpathExpression);
                foreach ($relationItems as $relationItem) {
                    $relationItem->parentNode->removeChild($relationItem);
                }

                $this->databaseConnection->prepare(
                    sprintf(
                        "UPDATE ezcontentobject_attribute SET data_text='%s' WHERE id='%s' and version='%s'",
                        $document->saveXML(),
                        $row['id'],
                        $row['version']
                    )
                )->execute();

                foreach ($this->cacheTagRepository->getCollectors() as $collector) {
                    $cacheTags = array_merge(
                        $cacheTags,
                        $collector->getFieldTagsOnContentDelete($row['id'])
                    );
                }
            }

            foreach ($this->cacheTagRepository->getCollectors() as $collector) {
                $cacheTags = array_merge(
                    $cacheTags,
                    $collector->getTagsOnContentDelete($contentId)
                );
            }

            $this->cacheProxyAdapter->invalidateTags($cacheTags);
        }
    }
}