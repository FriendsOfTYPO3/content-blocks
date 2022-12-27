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

namespace TYPO3\CMS\ContentBlocks\Domain\Repository;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\CodeGenerator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Definition\ContentElementDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Service\ConfigurationService;
use TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Finds the configuration of all ContentBlocks or a single ContentBlock.
 */
class ContentBlockConfigurationRepository implements SingletonInterface
{
    protected string $publicPath = '';
    protected string $privatePath = '';

    public function __construct()
    {
        // create dir if not exists
        GeneralUtility::mkdir_deep(ConfigurationService::getContentBlockLegacyPath());
    }


    public function findAll(): TableDefinitionCollection
    {
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth(0)->in(ConfigurationService::getContentBlockLegacyPath());

        foreach ($cbFinder as $splPath) {
            if (!is_readable($splPath->getPathname() . '/composer.json')) {
                throw new \RuntimeException('Cannot read or find composer.json file in "' . $splPath->getPathname() . '"' . '/composer.json');
            }
            $composerJson = json_decode(file_get_contents($splPath->getPathname() . '/composer.json'), true);
            if ($composerJson['type'] !== ConfigurationService::getComposerType()) {
                continue;
            }
            $nameFromComposer = explode('/', $composerJson['name']);
            $result[] = $this->findByIdentifier($nameFromComposer[1]);
        }

        return TableDefinitionCollection::createFromArray($result);
    }

    /**
     * Writes a ContentBlock to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConf): self
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . $contentBlockConf->package;
        // check if directory exists, if so, stop.
        if (file_exists($cbBasePath)) {
            throw new \RuntimeException('A content block with the identifier "' . $contentBlockConf->package . '" already exists.');
        }
        $htmlTemplateGenerator = GeneralUtility::makeInstance(HtmlTemplateCodeGenerator::class);

        // create directory structure
        mkdir($cbBasePath);
        $cbBasePath .= '/';
        mkdir($cbBasePath . ConfigurationService::getContentBlocksPublicPath(), 0777, true);
        mkdir($cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language', 0777, true);

        // create files
        file_put_contents(
            $cbBasePath . 'composer.json',
            json_encode($contentBlockConf->composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorPreview.html',
            $htmlTemplateGenerator->getHtmlTemplateEditorPreview($contentBlockConf)
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Frontend.html',
            $htmlTemplateGenerator->getHtmlTemplateFrontend($contentBlockConf)
        );
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'EditorPreview.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'Frontend.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPublicPath() . '/' . 'Frontend.js',
            '/* Created by Content BlockWizard */'
        );

        // TODO: "transfer" the icon file
        return $this;
    }

    /**
     * At the moment: updates only the yaml file and the translations.
     */
    public function update(ContentBlockConfiguration $contentBlockConf): self
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . '/' . $contentBlockConf->package;

        // check if directory exists, if not, create a new ContentBlock.
        if (!file_exists($cbBasePath)) {
            return $this->create($contentBlockConf);
        }

        // update the yaml file
        file_put_contents(
            $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );

        // Update translations
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        return $this;
    }
    public function findByIdentifier(string $identifier): array
    {
        $cbBasePath = ConfigurationService::getContentBlockLegacyPath() . '/' . $identifier;
        if (!file_exists($cbBasePath)) {
            throw new \RuntimeException('Content block "' . $identifier . '" could not be found in "' . $cbBasePath . '".');
        }
        // TODO: Validator check if the content block can be processed
        $cbConf = [];
        $cbConf['composerJson'] = json_decode(
            file_get_contents($cbBasePath . '/' . 'composer.json'),
            true
        );
        $cbConf['yaml'] = Yaml::parseFile(
            $cbBasePath . '/' . ConfigurationService::getContentBlocksPrivatePath() . '/' . 'EditorInterface.yaml',
        );

        // icon
        // directory paths (relative to publicPath())
        $path = ConfigurationService::getContentBlockLegacyPath() . '/' . $identifier;
        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $ext) {
            $checkIconPath = GeneralUtility::getFileAbsFileName(
                $path . '/' . ConfigurationService::getContentBlocksPublicPath() . '/' . 'ContentBlockIcon.' . $ext
            );
            if (is_readable($checkIconPath)) {
                $iconPath = $path . '/' . ConfigurationService::getContentBlocksPublicPath() . '/' . 'ContentBlockIcon.' . $ext;
                $iconProviderClass = $ext === 'svg'
                    ? SvgIconProvider::class
                    : BitmapIconProvider::class;
                break;
            }
        }
        $cbConf['icon'] = $iconPath;
        $cbConf['iconProvider'] = $iconProviderClass;

        if ($iconPath === null) {
            throw new \RuntimeException('No icon could be found for content block "' . $identifier . '" in path "' . $path . '".');
        }
        return $cbConf;
    }

    /**
     * Find a ContentBlock by CType
     */
    public function findContentBlockByCType(string $cType): TableDefinitionCollection
    {
        $cbFinder = new Finder();
        $cbFinder->directories()->depth('== 0')->in($this->hostBasePath);

        foreach ($cbFinder as $splPath) {
            // directory paths (full)
            $realPath = $splPath->getPathname() . '/';

            // composer.json
            if (!is_readable($realPath . 'composer.json')) {
                throw new \Exception(sprintf('Cannot read or find composer.json file: %s', $realPath . 'composer.json'));
            }

            $composerJson = json_decode(file_get_contents($realPath . 'composer.json'), true);

            if ($composerJson['type'] !== ConfigurationService::getComposerType()) {
                continue;
            }

            $nameFromComposer = explode('/', $composerJson['name']);
            $tempCTypeformContentBlock = 'cb_' . str_replace('/', '-', $composerJson['name']);

            if ($cType === $tempCTypeformContentBlock) {
                $result = [
                    0 => $this->findByIdentifier($nameFromComposer[1]),
                ];
                return TableDefinitionCollection::createFromArray($result);
            }
        }

        return new TableDefinitionCollection;
    }

    public function findContentElementDefinition(string $cType): ContentElementDefinition
    {
        /** @var TableDefinitionCollection $cbConfiguration */
        $tableDefinitionColleciton = $this->findContentBlockByCType($cType);

        $contentElementDefinition = false;
        /** @var TableDefinition $tableDefinition */
        foreach ($tableDefinitionColleciton as $tableDefinition) {
            /** @var TypeDefinition|ContentElementDefinition $typeDefinition */
            foreach ($tableDefinition->getTypeDefinitionCollection() as $typeDefinition) {
                if ($typeDefinition instanceof ContentElementDefinition) {
                    return $typeDefinition;
                }
            }
        }

        if ($contentElementDefinition === false) {
            throw new \Exception(sprintf('It seems you try to render a ContentBlock which does not exists. The unknown CType is: %s. Reason: We couldn\'t find the composer package.', $cType));
        }
    }
}
