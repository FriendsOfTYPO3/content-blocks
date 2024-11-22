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

namespace TYPO3\CMS\ContentBlocks\Schema\Capability;

use TYPO3\CMS\ContentBlocks\Schema\Field\TcaFieldTypeInterface;

/**
 * Contains all information if a schema is language-aware, meaning
 * it has a "languageField", a "translationOrigPointerField", maybe a "translationSourceField"
 * and maybe a "diffSourceField".
 *
 * @internal Not part of TYPO3's public API.
 */
readonly class LanguageAwareSchemaCapability implements SchemaCapabilityInterface
{
    public function __construct(
        protected TcaFieldTypeInterface $languageField,
        protected TcaFieldTypeInterface $originPointerField,
        protected ?TcaFieldTypeInterface $translationSourceField,
        protected ?TcaFieldTypeInterface $diffSourceField
    ) {}

    public function getLanguageField(): TcaFieldTypeInterface
    {
        return $this->languageField;
    }

    public function getTranslationOriginPointerField(): TcaFieldTypeInterface
    {
        return $this->originPointerField;
    }

    public function hasTranslationSourceField(): bool
    {
        return $this->translationSourceField !== null;
    }

    public function getTranslationSourceField(): ?TcaFieldTypeInterface
    {
        return $this->translationSourceField;
    }

    public function getDiffSourceField(): ?TcaFieldTypeInterface
    {
        return $this->diffSourceField;
    }

    public function hasDiffSourceField(): bool
    {
        return $this->diffSourceField !== null;
    }
}
