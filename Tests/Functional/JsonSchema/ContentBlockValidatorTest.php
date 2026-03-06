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

namespace TYPO3\CMS\ContentBlocks\Tests\Functional\JsonSchema;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\ContentBlockValidator;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

class ContentBlockValidatorTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'typo3conf/ext/content_blocks/Build/content_blocks_examples',
        'typo3conf/ext/content_blocks',
    ];

    public static function canValidateContentBlockDataProvider(): iterable
    {
        return [
            ['example/accordion'],
            ['example/file-type-image'],
            ['example/example-page-type'],
            ['hov/notype'],
            ['hov/record1'],
        ];
    }

    #[Test]
    #[DataProvider('canValidateContentBlockDataProvider')]
    public function canValidateContentBlock(string $contentBlockName): void
    {
        $contentBlockRegistry = $this->get(ContentBlockRegistry::class);
        $contentBlockValidator = new ContentBlockValidator(new JsonSchemaValidator());

        $testContentBlock = $contentBlockRegistry->getContentBlock($contentBlockName);
        $result = $contentBlockValidator->validateContentBlock($testContentBlock);

        self::assertFalse($result->hasError());
    }
}
