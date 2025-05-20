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

namespace TYPO3\CMS\ContentBlocks\Tests\Unit\Builder;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\ContentBlocks\Builder\ConfigBuilder;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class ConfigBuilderTest extends UnitTestCase
{
    #[Test]
    public function defaultsAreSet(): void
    {
        $configBuilder = new ConfigBuilder();
        $defaults = [
            'vendorPrefix' => 'acme',
            'prefixType' => 'vendor',
            'group' => 'custom',
        ];

        $result = $configBuilder->build(
            ContentType::CONTENT_ELEMENT,
            'my-vendor',
            'my-name',
            'Title ABC',
            'custom-element',
            $defaults
        );

        self::assertSame('my-vendor/my-name', $result['name']);
        self::assertSame('Title ABC', $result['title']);
        self::assertSame('acme', $result['vendorPrefix']);
        self::assertSame('vendor', $result['prefixType']);
        self::assertSame('custom', $result['group']);
    }
}
