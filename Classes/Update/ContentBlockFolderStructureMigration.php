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

namespace TYPO3\CMS\ContentBlocks\Update;

use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('contentBlocksFolderStructureMigration')]
readonly class ContentBlockFolderStructureMigration implements UpgradeWizardInterface
{
    public function __construct(
        protected PackageManager $packageManager,
    ) {}

    public function getTitle(): string
    {
        return 'Content Block folder structure migration';
    }

    public function getDescription(): string
    {
        return 'Migrates to the new lowercase folder structure for single Content Blocks.';
    }

    public function executeUpdate(): bool
    {
        foreach ($this->findContentBlockPaths() as $path) {
            $this->migrate($path);
        }
        return true;
    }

    public function updateNecessary(): bool
    {
        return $this->findContentBlockPaths() !== [];
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    protected function findContentBlockPaths(): array
    {
        $contentBlockPaths = [];
        foreach ($this->packageManager->getActivePackages() as $package) {
            $contentElementsFolder = $package->getPackagePath() . 'ContentBlocks/ContentElements';
            $pageTypesFolder = $package->getPackagePath() . 'ContentBlocks/PageTypes';
            $recordTypesFolder = $package->getPackagePath() . 'ContentBlocks/RecordTypes';
            if (is_dir($contentElementsFolder)) {
                $contentBlockPaths[] = $this->loadContentBlocksInExtension($contentElementsFolder);
            }
            if (is_dir($pageTypesFolder)) {
                $contentBlockPaths[] = $this->loadContentBlocksInExtension($pageTypesFolder);
            }
            if (is_dir($recordTypesFolder)) {
                $contentBlockPaths[] = $this->loadContentBlocksInExtension($recordTypesFolder);
            }
        }
        $contentBlockPaths = array_merge(...$contentBlockPaths);
        return $contentBlockPaths;
    }

    /**
     * @return string[]
     */
    protected function loadContentBlocksInExtension(string $path): array
    {
        $result = [];
        $finder = new Finder();
        $finder->directories()->depth(0)->in($path);
        foreach ($finder as $splFileInfo) {
            $absoluteContentBlockPath = $splFileInfo->getPathname();
            if (file_exists($absoluteContentBlockPath . '/EditorInterface.yaml')) {
                $result[] = $absoluteContentBlockPath;
            }
        }
        return $result;
    }

    protected function migrate(string $path): void
    {
        rename($path . '/EditorInterface.yaml', $path . '/config.yaml');
        if (file_exists($path . '/Source')) {
            rename($path . '/Source', $path . '/templates');
        }
        if (file_exists($path . '/templates/Frontend.html')) {
            rename($path . '/templates/Frontend.html', $path . '/templates/frontend.html');
        }
        if (file_exists($path . '/templates/Partials')) {
            rename($path . '/templates/Partials', $path . '/templates/partials');
        }
        if (file_exists($path . '/templates/Layouts')) {
            rename($path . '/templates/Layouts', $path . '/templates/layouts');
        }
        if (file_exists($path . '/templates/EditorPreview.html')) {
            rename($path . '/templates/EditorPreview.html', $path . '/templates/backend-preview.html');
        }
        if (file_exists($path . '/templates/Language')) {
            rename($path . '/templates/Language', $path . '/language');
        }
        if (file_exists($path . '/language/Labels.xlf')) {
            rename($path . '/language/Labels.xlf', $path . '/language/labels.xlf');
            $this->migrateLocalizedLabelFiles($path . '/language');
        }
        if (file_exists($path . '/Assets')) {
            rename($path . '/Assets', $path . '/assets');
        }
        if (file_exists($path . '/assets/Icon.svg')) {
            rename($path . '/assets/Icon.svg', $path . '/assets/icon.svg');
        }
        if (file_exists($path . '/assets/IconHideInMenu.svg')) {
            rename($path . '/assets/IconHideInMenu.svg', $path . '/assets/icon-hide-in-menu.svg');
        }
        if (file_exists($path . '/assets/IconRoot.svg')) {
            rename($path . '/assets/IconRoot.svg', $path . '/assets/icon-root.svg');
        }
    }

    protected function migrateLocalizedLabelFiles(string $languagePath): void
    {
        $finder = new Finder();
        $finder->files()->name('*.xlf')->in($languagePath);
        foreach ($finder as $splFileInfo) {
            $fileName = $splFileInfo->getFilename();
            $parts = explode('.', $fileName);
            if (count($parts) !== 3) {
                continue;
            }
            $parts[1] = 'labels';
            $newFileName = implode('.', $parts);
            rename($splFileInfo->getPathname(), $languagePath . '/' . $newFileName);
        }
    }
}
