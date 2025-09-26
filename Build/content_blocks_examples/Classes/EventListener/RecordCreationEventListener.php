<?php

declare(strict_types=1);

namespace ContentBlocks\Examples\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Event\RecordCreationEvent;
use TYPO3\CMS\Core\Domain\RecordFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsEventListener(identifier: 'example/recordCreation')]
final readonly class RecordCreationEventListener
{
    public function __construct(
        protected RecordFactory $recordFactory,
        protected ConnectionPool $connectionPool,
    ) {}

    public function __invoke(RecordCreationEvent $event): void
    {
        $rawRecord = $event->getRawRecord();
        if ($rawRecord->getMainType() !== 'tt_content') {
            return;
        }
        if ($rawRecord->getRecordType() !== 'example_page_list') {
            return;
        }
        if ($rawRecord->has('tx_sitepackage_page') === false) {
            return;
        }
        $pages = $rawRecord->get('tx_sitepackage_page');
        $parentPageIds = GeneralUtility::intExplode(',', $pages, true);
        $queryBuilder = $this->connectionPool
            ->getQueryBuilderForTable('pages');
        $pageItemEntries = $queryBuilder
            ->select('*')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in(
                    'pid',
                    $queryBuilder->createNamedParameter($parentPageIds, Connection::PARAM_INT_ARRAY)
                ),
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $pageItems = [];
        foreach ($pageItemEntries as $pageItem) {
            $pageItems[] = $this->recordFactory->createResolvedRecordFromDatabaseRow('pages', $pageItem);
        }

        $event->setProperty('pageItems', $pageItems);
    }
}
