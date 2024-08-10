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

namespace TYPO3\CMS\ContentBlocks\Service\Icon;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;

/**
 * @internal Not part of TYPO3's public API.
 */
final class ContentTypeIconResolverInput
{
    public function __construct(
        public string $name,
        public string $absolutePath,
        public string $extension,
        public string $identifier,
        public ContentType $contentType,
        public string $table,
        public int|string $typeName,
        public string $suffix = '',
    ) {}
}
