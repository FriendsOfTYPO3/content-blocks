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

use Opis\JsonSchema\Validator;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class JsonSchemaValidator
{
    public function isValidContentElement(object $data): bool
    {
        $validator = $this->createValidatorForContentElement();
        $result = $validator->validate($data, 'http://typo3.org/content-element.json');
        return !$result->hasError();
    }

    public function isValidPageType(object $data): bool
    {
        $validator = $this->createValidatorForPageType();
        $result = $validator->validate($data, 'http://typo3.org/page-type.json');
        return !$result->hasError();
    }

    public function isValidRecordType(object $data): bool
    {
        $validator = $this->createValidatorForRecordType();
        $result = $validator->validate($data, 'http://typo3.org/record-type.json');
        return !$result->hasError();
    }

    public function isValidFileType(object $data): bool
    {
        $validator = $this->createValidatorForFileType();
        $result = $validator->validate($data, 'http://typo3.org/file-type.json');
        return !$result->hasError();
    }

    protected function createValidatorForContentElement(): Validator
    {
        $validator = new Validator();
        $validator->resolver()->registerFile(
            'http://typo3.org/content-element.json',
            __DIR__ . '/../../JsonSchema/content-element.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForPageType(): Validator
    {
        $validator = new Validator();
        $validator->resolver()->registerFile(
            'http://typo3.org/page-type.json',
            __DIR__ . '/../../JsonSchema/page-type.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForRecordType(): Validator
    {
        $validator = new Validator();
        $validator->resolver()->registerFile(
            'http://typo3.org/record-type.json',
            __DIR__ . '/../../JsonSchema/record-type.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForFileType(): Validator
    {
        $validator = new Validator();
        $validator->resolver()->registerFile(
            'http://typo3.org/file-type.json',
            __DIR__ . '/../../JsonSchema/file-type.schema.json'
        );
        return $validator;
    }
}
