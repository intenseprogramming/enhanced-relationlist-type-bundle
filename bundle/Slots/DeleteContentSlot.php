<?php

namespace IntProg\EnhancedRelationListBundle\Slots;

use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
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
     * @var DatabaseHandler
     */
    protected $databaseHandler;

    /**
     * @var InMemoryClearingProxyAdapter
     */
    protected $cacheProxyAdapter;

    /**
     * @var CacheTagRepository
     */
    protected $cacheTagRepository;

    /**
     * @param DatabaseHandler              $databaseHandler
     * @param InMemoryClearingProxyAdapter $cacheProxyAdapter
     * @param CacheTagRepository           $cacheTagRepository
     */
    public function __construct(
        DatabaseHandler $databaseHandler,
        InMemoryClearingProxyAdapter $cacheProxyAdapter,
        CacheTagRepository $cacheTagRepository
    ) {
        $this->databaseHandler    = $databaseHandler;
        $this->cacheProxyAdapter  = $cacheProxyAdapter;
        $this->cacheTagRepository = $cacheTagRepository;
    }

    /**
     * Removes relation
     *
     * @param Signal $signal
     *
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
            $query = $this->databaseHandler->createSelectQuery();
            $query
                ->select('ezcontentobject_attribute.*')
                ->from('ezcontentobject_attribute')
                ->where(
                    sprintf(
                        "data_type_string = '%s' and data_text like '%%content-id=\"%s\"%%'",
                        Type::FIELD_TYPE_IDENTIFIER,
                        $contentId
                    )
                );

            $statement = $query->prepare();
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

                $query = $this->databaseHandler->createUpdateQuery();
                $query
                    ->update('ezcontentobject_attribute')
                    ->set(
                        'data_text',
                        $query->bindValue($document->saveXML(), null, PDO::PARAM_STR)
                    )
                    ->where(
                        $query->expr->lAnd(
                            $query->expr->eq(
                                $this->databaseHandler->quoteColumn('id'),
                                $query->bindValue($row['id'], null, PDO::PARAM_INT)
                            ),
                            $query->expr->eq(
                                $this->databaseHandler->quoteColumn('version'),
                                $query->bindValue($row['version'], null, PDO::PARAM_INT)
                            )
                        )
                    );
                $query->prepare()->execute();

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