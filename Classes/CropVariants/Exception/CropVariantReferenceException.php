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

namespace TYPO3\CMS\ContentBlocks\CropVariants\Exception;

use TYPO3\CMS\Core\Exception;

/**
 * Thrown if a File field references a central Crop Variant which is not
 * registered. Carries the Content Block, the field and the Crop Variant key
 * so that the error can be located.
 *
 * @internal Not part of TYPO3's public API.
 */
final class CropVariantReferenceException extends Exception
{
    public function __construct(
        private readonly string $reference,
        private readonly string $fieldIdentifier,
        private readonly string $cropVariantKey,
        private readonly string $contentBlockName = '',
        ?\Throwable $previous = null,
    ) {
        $message = 'Crop Variant "' . $reference . '" referenced in "' . $this->getPath() . '"';
        if ($contentBlockName !== '') {
            $message .= ' of Content Block "' . $contentBlockName . '"';
        }
        $message .= ' is not registered.';
        parent::__construct($message, 1794583201, $previous);
    }

    public function withContentBlockName(string $contentBlockName): self
    {
        return new self($this->reference, $this->fieldIdentifier, $this->cropVariantKey, $contentBlockName, $this->getPrevious());
    }

    public function getContentBlockName(): string
    {
        return $this->contentBlockName;
    }

    /**
     * JSON pointer like path, matching the path column of the lint command.
     */
    public function getPath(): string
    {
        return '/fields/' . $this->fieldIdentifier . '/cropVariants/' . $this->cropVariantKey;
    }
}
