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

namespace TYPO3\CMS\ContentBlocks\Validation;

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class PageTypeNameValidator
{
    /** @var list<int> $reservedPageTypes */
    protected static array $reservedPageTypes = [
        PageRepository::DOKTYPE_DEFAULT,
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
        PageRepository::DOKTYPE_BE_USER_SECTION,
        PageRepository::DOKTYPE_SPACER,
        PageRepository::DOKTYPE_SYSFOLDER,
    ];

    /** @var list<int> $nonOverridablePageTypes */
    protected static array $nonOverridablePageTypes = [
        PageRepository::DOKTYPE_SPACER,
        PageRepository::DOKTYPE_SYSFOLDER,
    ];

    public static function isExistingPageType(string|int $typeName): bool
    {
        $integerTypeName = (int)$typeName;
        $isExistingPageType = in_array($integerTypeName, self::$reservedPageTypes, true);
        return $isExistingPageType;
    }

    public static function validate(string|int $typeName, string $contentBlockName): void
    {
        $integerTypeName = (int)$typeName;
        if (!MathUtility::canBeInterpretedAsInteger($typeName) || $integerTypeName < 0 || in_array($integerTypeName, self::$nonOverridablePageTypes, true)) {
            throw new \InvalidArgumentException(
                'Invalid value "' . $typeName . '" for "typeName" in ContentBlock "' .
                $contentBlockName . '". Value must be a positive integer and not one of the non-overridable page types: '
                . implode(', ', self::$nonOverridablePageTypes),
                1689287031
            );
        }
    }
}
