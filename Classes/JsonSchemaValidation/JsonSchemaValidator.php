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
use Opis\JsonSchema\Validator;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class JsonSchemaValidator
{
    public function validateContentElement(object $data): ValidationResult
    {
        $validator = $this->createValidatorForContentElement();
        $result = $validator->validate($data, 'http://typo3.org/content-element.json');
        return $result;
    }

    public function validatePageType(object $data): ValidationResult
    {
        $validator = $this->createValidatorForPageType();
        $result = $validator->validate($data, 'http://typo3.org/page-type.json');
        return $result;
    }

    public function validateRecordType(object $data): ValidationResult
    {
        $validator = $this->createValidatorForRecordType();
        $result = $validator->validate($data, 'http://typo3.org/record-type.json');
        return $result;
    }

    public function validateFileType(object $data): ValidationResult
    {
        $validator = $this->createValidatorForFileType();
        $result = $validator->validate($data, 'http://typo3.org/file-type.json');
        return $result;
    }

    public function validateBasic(object $data): ValidationResult
    {
        $validator = $this->createValidatorForBasic();
        $result = $validator->validate($data, 'http://typo3.org/basic.json');
        return $result;
    }

    public function isValidContentElement(object $data): bool
    {
        $result = $this->validateContentElement($data);
        return !$result->hasError();
    }

    public function isValidPageType(object $data): bool
    {
        $result = $this->validatePageType($data);
        return !$result->hasError();
    }

    public function isValidRecordType(object $data): bool
    {
        $result = $this->validateRecordType($data);
        return !$result->hasError();
    }

    public function isValidFileType(object $data): bool
    {
        $result = $this->validateFileType($data);
        return !$result->hasError();
    }

    protected function createValidatorForContentElement(): Validator
    {
        $validator = (new Validator())
            ->setStopAtFirstError(false)
            ->setMaxErrors(100);
        $validator->resolver()->registerFile(
            'http://typo3.org/content-element.json',
            __DIR__ . '/../../JsonSchema/content-element.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForPageType(): Validator
    {
        $validator = $this->createValidator();
        $validator->resolver()->registerFile(
            'http://typo3.org/page-type.json',
            __DIR__ . '/../../JsonSchema/page-type.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForRecordType(): Validator
    {
        $validator = $this->createValidator();
        $validator->resolver()->registerFile(
            'http://typo3.org/record-type.json',
            __DIR__ . '/../../JsonSchema/record-type.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForFileType(): Validator
    {
        $validator = $this->createValidator();
        $validator->resolver()->registerFile(
            'http://typo3.org/file-type.json',
            __DIR__ . '/../../JsonSchema/file-type.schema.json'
        );
        return $validator;
    }

    protected function createValidatorForBasic(): Validator
    {
        $validator = (new Validator())
            ->setStopAtFirstError(false)
            ->setMaxErrors(100);
        $validator->resolver()->registerFile(
            'http://typo3.org/basic.json',
            __DIR__ . '/../../JsonSchema/basic.schema.json'
        );
        return $validator;
    }

    protected function createValidator(): Validator
    {
        $validator = new Validator();
        return $validator;
    }
}
