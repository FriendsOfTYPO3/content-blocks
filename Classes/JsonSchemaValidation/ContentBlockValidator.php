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

namespace TYPO3\CMS\ContentBlocks\JsonSchemaValidation;

use Opis\JsonSchema\ValidationResult;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

readonly class ContentBlockValidator
{
    public function __construct(
        protected JsonSchemaValidator $jsonSchemaValidator
    ) {}

    public function validate(LoadedContentBlock $contentBlock): ValidationResult
    {
        $data = $contentBlock->getYaml();
        $dataJson = json_encode($data);
        $dataObject = json_decode($dataJson);
        $validationResult = match ($contentBlock->getContentType()) {
            ContentType::CONTENT_ELEMENT => $this->jsonSchemaValidator->validateContentElement($dataObject),
            ContentType::PAGE_TYPE => $this->jsonSchemaValidator->validatePageType($dataObject),
            ContentType::RECORD_TYPE => $this->jsonSchemaValidator->validateRecordType($dataObject),
            ContentType::FILE_TYPE => $this->jsonSchemaValidator->validateFileType($dataObject),
        };
        return $validationResult;
    }
}
