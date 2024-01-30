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

namespace TYPO3\CMS\ContentBlocks\UserFunction;

use TYPO3\CMS\ContentBlocks\Service\TtContentParentField;

/**
 * @internal
 */
final class ContentWhere
{
    public function __construct(
        private readonly TtContentParentField $ttContentParentField
    ) {}

    public function extend(string $where): string
    {
        $columns = array_map(
            fn(string $fieldName): string => ' AND {#' . $fieldName . '} = 0',
            $this->ttContentParentField->getAllFieldNames()
        );

        return $where . implode(' ', $columns);
    }
}
