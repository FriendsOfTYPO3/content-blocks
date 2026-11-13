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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Command;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Basics\BasicsLoader;
use TYPO3\CMS\ContentBlocks\Command\LintContentBlocksCommand;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\ContentBlockValidator;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaErrorFormatter;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaValidator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class LintContentBlocksCommandTest extends UnitTestCase
{
    #[Test]
    public function rootErrorIsGatheredAlongsideNestedError(): void
    {
        $yaml = [
            'identifier' => 'Vendor/Basic',
            'unknownRootOption' => true,
            'fields' => [
                [
                    'identifier' => 'text',
                    'type' => 'Text',
                    'unknownOption' => true,
                ],
            ],
        ];
        $command = new LintContentBlocksCommand(
            $this->createMock(ContentBlockLoader::class),
            new ContentBlockValidator(new JsonSchemaValidator()),
            new JsonSchemaErrorFormatter(),
            $this->createMock(BasicsLoader::class),
        );
        $validationResult = (new JsonSchemaValidator())->validateBasic(json_decode(json_encode($yaml)));

        $errors = (new \ReflectionMethod($command, 'gatherErrors'))->invoke($command, $validationResult, $yaml);

        $expected = [
            ['/fields/text', 'Additional object properties are not allowed: unknownOption'],
            ['/', 'Additional object properties are not allowed: unknownRootOption'],
        ];
        self::assertSame($expected, $errors);
    }
}
