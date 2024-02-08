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

/**
 * @internal
 */
class ContentWhere
{
    public function __construct(
        protected readonly array $parentFieldNames
    ) {}

    public function extend(string $where): string
    {
        $columns = array_map(
            fn(string $fieldName): string => ' AND {#' . $fieldName . '} = 0',
            $this->parentFieldNames
        );

        return $where . implode(' ', $columns);
    }
}
