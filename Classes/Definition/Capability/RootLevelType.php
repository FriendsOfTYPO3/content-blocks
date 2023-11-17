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

namespace TYPO3\CMS\ContentBlocks\Definition\Capability;

/**
 * @internal Not part of TYPO3's public API.
 */
enum RootLevelType: string
{
    case ONLY_ON_PAGES = 'onlyOnPages';
    case ONLY_ON_ROOT_LEVEL = 'onlyOnRootLevel';
    case BOTH = 'both';

    public function getTcaValue(): int
    {
        return match ($this) {
            self::ONLY_ON_PAGES => 0,
            self::ONLY_ON_ROOT_LEVEL => 1,
            self::BOTH => -1,
        };
    }
}
