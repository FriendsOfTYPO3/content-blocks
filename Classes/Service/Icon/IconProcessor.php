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

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;

/**
 * @internal Not part of TYPO3's public API.
 */
class IconProcessor
{
    /** @var callable[] */
    protected array $instructions = [];

    public function process(): void
    {
        while ($instruction = array_shift($this->instructions)) {
            $instruction();
        }
    }

    public function addInstruction(ContentTypeIcon $contentTypeIcon, ContentTypeIconResolverInput $input): void
    {
        $instruction = function () use ($contentTypeIcon, $input) {
            $resultIcon = ContentTypeIconResolver::resolve($input);
            $contentTypeIcon->iconIdentifier = $resultIcon->iconIdentifier;
            $contentTypeIcon->iconPath = $resultIcon->iconPath;
            $contentTypeIcon->iconProvider = $resultIcon->iconProvider;
        };
        $this->instructions[] = $instruction;
    }
}
