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
    public function validate(object $data, string $schemaIdentifier): bool
    {
        $validator = new Validator();
        $pathToSchemaFile = __DIR__ . '/../../JsonSchema/content-element.schema.json';
        $validator->resolver()->registerFile(
            'http://typo3.org/content-element.json',
            $pathToSchemaFile
        );
        $result = $validator->validate($data, $schemaIdentifier);
        return $result->hasError();
    }
}
