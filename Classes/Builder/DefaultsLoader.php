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

namespace TYPO3\CMS\ContentBlocks\Builder;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DefaultsLoader
{
    /**
     * @return array{
     *     content-type: string,
     *     vendor: ?string,
     *     skeleton-path: string,
     *     extension: ?string,
     *     config: array<string, mixed>
     * }
     */
    public function loadDefaultsFromContentBlocksConfig(?string $configPath): array
    {
        $config = [
            'content-type' => 'content-element',
            'vendor' => null,
            'skeleton-path' => 'content-blocks-skeleton',
            'extension' => null,
            'config' => [],
        ];
        $currentDirectory = getcwd();
        if ($currentDirectory === false) {
            return $config;
        }
        $configFile = $configPath ?? 'content-blocks.yaml';
        $path = $currentDirectory . '/' . $configFile;
        $path = GeneralUtility::fixWindowsFilePath($path);
        if (!file_exists($path)) {
            return $config;
        }
        try {
            $yaml = Yaml::parseFile($path);
        } catch (ParseException) {
            return $config;
        }
        if (!is_array($yaml)) {
            return $config;
        }
        foreach (array_keys($config) as $key) {
            $config[$key] = $yaml[$key] ?? $config[$key];
        }
        return $config;
    }
}
