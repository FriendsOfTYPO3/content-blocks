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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\JsonSchemaValidation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class RecordTypeSchemaTest extends UnitTestCase
{
    public static function recordTypeSchemaValidationWorksAsExpectedDataProvider(): iterable
    {
        yield 'Basic valid Record Type' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'fields' => [
                    (object)[
                        'identifier' => 'title',
                        'type' => 'Text',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid labelField as string' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'labelField' => 'title',
            ],
            'valid' => true,
        ];

        yield 'Valid labelField as array of strings' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'labelField' => ['title', 'subtitle'],
            ],
            'valid' => true,
        ];

        yield 'Valid fallbackLabelFields' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'fallbackLabelFields' => ['subtitle', 'description'],
            ],
            'valid' => true,
        ];

        yield 'Valid restrictions' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'restriction' => (object)[
                    'disabled' => false,
                    'startTime' => true,
                    'endTime' => false,
                    'userGroup' => true,
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid sortField as string' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'sortField' => 'title',
            ],
            'valid' => true,
        ];

        yield 'Valid sortField as array of objects' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'sortField' => [
                    (object)[
                        'identifier' => 'title',
                        'order' => 'asc',
                    ],
                    (object)[
                        'identifier' => 'crdate',
                        'order' => 'desc',
                    ],
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid security settings' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'security' => (object)[
                    'ignoreWebMountRestriction' => true,
                    'ignoreRootLevelRestriction' => false,
                    'ignorePageTypeRestriction' => true,
                ],
            ],
            'valid' => true,
        ];

        yield 'Valid rootLevelType enum' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'rootLevelType' => 'onlyOnRootLevel',
            ],
            'valid' => true,
        ];

        yield 'Valid booleans' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'languageAware' => false,
                'workspaceAware' => true,
                'editLocking' => false,
                'softDelete' => true,
                'trackCreationDate' => false,
                'trackUpdateDate' => true,
                'sortable' => false,
                'internalDescription' => true,
                'readOnly' => true,
                'adminOnly' => false,
                'hideAtCopy' => true,
            ],
            'valid' => true,
        ];

        yield 'Valid appendLabelAtCopy' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'appendLabelAtCopy' => ' (Copy)',
            ],
            'valid' => true,
        ];

        yield 'Valid group and other common properties' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'group' => 'custom',
                'title' => 'Record Title',
                'prefixFields' => true,
                'prefixType' => 'full',
                'vendorPrefix' => 'hov',
                'priority' => 50,
            ],
            'valid' => true,
        ];

        yield 'Negative: unknown property at root level' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'unknown' => 'property',
            ],
            'valid' => false,
        ];

        yield 'Negative: unknown property in restriction' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'restriction' => (object)[
                    'unknown' => true,
                ],
            ],
            'valid' => false,
        ];

        yield 'Negative: invalid rootLevelType enum' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'rootLevelType' => 'invalid',
            ],
            'valid' => false,
        ];

        yield 'Negative: invalid sortField order enum' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 'record1',
                'sortField' => [
                    (object)[
                        'identifier' => 'title',
                        'order' => 'invalid',
                    ],
                ],
            ],
            'valid' => false,
        ];

        yield 'Negative: typeName must be string' => [
            'data' => (object)[
                'name' => 'hov/record1',
                'table' => 'tx_hov_domain_model_record1',
                'typeName' => 123,
            ],
            'valid' => false,
        ];
    }

    #[Test]
    #[DataProvider('recordTypeSchemaValidationWorksAsExpectedDataProvider')]
    public function recordTypeSchemaValidationWorksAsExpected(object $data, bool $valid): void
    {
        $jsonSchemaValidator = new JsonSchemaValidator();

        $validationResult = $jsonSchemaValidator->isValidRecordType($data);

        self::assertSame($valid, $validationResult);
    }
}
