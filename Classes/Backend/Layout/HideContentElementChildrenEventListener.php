<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\ContentBlocks\Backend\Layout;

use TYPO3\CMS\Backend\View\Event\ModifyDatabaseQueryForContentEvent;
use TYPO3\CMS\Core\Database\Connection;

/**
 * @internal
 */
class HideContentElementChildrenEventListener
{
    public function __construct(
        protected readonly array $parentFieldNames,
    ) {}

    public function __invoke(ModifyDatabaseQueryForContentEvent $event): void
    {
        $queryBuilder = $event->getQueryBuilder();
        foreach ($this->parentFieldNames as $fieldName) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->eq(
                    $fieldName,
                    $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)
                )
            );
        }
        $event->setQueryBuilder($queryBuilder);
    }
}
