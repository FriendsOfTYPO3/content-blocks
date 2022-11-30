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
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentBlockConfigurationReporitory
 * Finds the configuration of all ContentBlocks or a single ContentBlock.
 */
class ContentBlockConfigurationRepository implements SingletonInterface
{
    protected ConfigurationService $configurationService;

    protected string $hostBasePath = '';

    protected string $publicPath = '';

    protected string $privatePath = '';

    public function __construct()
    {
        $this->configurationService = GeneralUtility::makeInstance(ConfigurationService::class);
        $this->hostBasePath = Environment::getPublicPath() . DIRECTORY_SEPARATOR . $this->configurationService->getBasePath();
        // create dir if not exists
        GeneralUtility::mkdir_deep($this->hostBasePath);
    }


    public function findAll(): TableDefinitionCollection
    {
        $result = [];
        $cbFinder = new Finder();
        $cbFinder->directories()->depth('== 0')->in($this->hostBasePath);

        foreach ($cbFinder as $splPath) {
            // directory paths (full)
            $realPath = $splPath->getPathname() . DIRECTORY_SEPARATOR;

            // composer.json
            if (!is_readable($realPath . 'composer.json')) {
                throw new \Exception(sprintf('Cannot read or find composer.json file: %s', $realPath . 'composer.json'));
            }

            $composerJson = json_decode(file_get_contents($realPath . 'composer.json'), true);

            if ($composerJson['type'] !== $this->configurationService->getComposerType()) {
                continue;
            }

            $nameFromComposer = explode('/', $composerJson['name']);

            // /** @var ContentBlockConfiguration $contentBlockConfiguration */
            // $contentBlockConfiguration = $this->findByIdentifier($nameFromComposer[1]);
            // $result[$contentBlockConfiguration->getKey()] = $contentBlockConfiguration;

            $result[] = $this->findByIdentifier($nameFromComposer[1]);
        }

        // return $result;
        return TableDefinitionCollection::createFromArray($result);
    }

    /**
     * Writes a ContentBlock to file system.
     */
    public function create(ContentBlockConfiguration $contentBlockConf): self
    {
        $cbBasePath = Environment::getPublicPath() . DIRECTORY_SEPARATOR . $this->configurationService->getContentBlockDestinationPath() . $contentBlockConf->package;
        // check if directory exists, if so, stop.
        if (is_dir($cbBasePath)) {
            throw new \Exception(sprintf('It seems your ContentBlock %s exists. Please make sure you create a new one.', $contentBlockConf->package));
        }
        $htmlTemplateGenerator = GeneralUtility::makeInstance(HtmlTemplateCodeGenerator::class);

        // create directory structure
        mkdir($cbBasePath);
        $cbBasePath .= DIRECTORY_SEPARATOR;
        mkdir($cbBasePath . $this->configurationService->getContentBlocksPublicPath(), 0777, true);
        mkdir($cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'Language', 0777, true);

        // create files
        file_put_contents(
            $cbBasePath . 'composer.json',
            json_encode($contentBlockConf->composerJson, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'EditorPreview.html',
            $htmlTemplateGenerator->getHtmlTemplateEditorPreview($contentBlockConf)
        );
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'Frontend.html',
            $htmlTemplateGenerator->getHtmlTemplateFrontend($contentBlockConf)
        );
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR . 'EditorPreview.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR . 'Frontend.css',
            '/* Created by Content BlockWizard */'
        );
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR . 'Frontend.js',
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
        $cbBasePath = Environment::getPublicPath() . DIRECTORY_SEPARATOR . $this->configurationService->getContentBlockDestinationPath() . $contentBlockConf->package;

        // check if directory exists, if not, create a new ContentBlock.
        if (!is_dir($cbBasePath)) {
            return $this->create($contentBlockConf);
        }

        // update the yaml file
        file_put_contents(
            $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'EditorInterface.yaml',
            Yaml::dump($contentBlockConf->yamlConfig, 10)
        );

        // udate translations
        if (count($contentBlockConf->labelsXlfContent) > 0) {
            foreach ($contentBlockConf->labelsXlfContent as $key => $translation) {
                $localLangPrefix = ($key === 'default' ? '' : $key . '.');
                file_put_contents(
                    $cbBasePath . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'Language/' . $localLangPrefix . 'Labels.xlf',
                    $translation
                );
            }
        }
        return $this;
    }

    /**
     * Find a ContentBlock by identifier
     */
    public function findByIdentifier(string $identifier): array
    {
        $cbBasePath = Environment::getPublicPath() . DIRECTORY_SEPARATOR . $this->configurationService->getContentBlockDestinationPath() . $identifier;
        // check if directory exists, if so, stop.
        if (!is_dir($cbBasePath)) {
            throw new \Exception(sprintf('You have tried to find a ContentBlock which does not exists: %s', $identifier));
        }
        // TODO: Validator check if the content block can be processed
        $cbConf = [];
        $cbConf['composerJson'] = json_decode(
            file_get_contents($cbBasePath . DIRECTORY_SEPARATOR . 'composer.json'),
            true
        );
        $cbConf['yaml'] = Yaml::parseFile(
            $cbBasePath . DIRECTORY_SEPARATOR . $this->configurationService->getContentBlocksPrivatePath() . DIRECTORY_SEPARATOR . 'EditorInterface.yaml',
        );

        // icon
        // directory paths (relative to publicPath())
        $path = $this->configurationService->getContentBlockDestinationPath() . $identifier . DIRECTORY_SEPARATOR;
        $iconPath = null;
        $iconProviderClass = null;
        foreach (['svg', 'png', 'gif'] as $ext) {
            $checkIconPath = GeneralUtility::getFileAbsFileName(
                $path . $this->configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR . 'ContentBlockIcon.' . $ext
            );
            if (is_readable($checkIconPath)) {
                $iconPath = $path . $this->configurationService->getContentBlocksPublicPath() . DIRECTORY_SEPARATOR . 'ContentBlockIcon.' . $ext;
                $iconProviderClass = $ext === 'svg'
                    ? SvgIconProvider::class
                    : BitmapIconProvider::class;
                break;
            }
        }
        $cbConf['icon'] = $iconPath;
        $cbConf['iconProvider'] = $iconProviderClass;

        if ($iconPath === null) {
            throw new \Exception(sprintf('No icon found for ContentBlock %s in path %s', $identifier, $path));
        }

        // /** @var ContentBlockConfigurationFactory $contentBlockConfFactory */
        // $contentBlockConfFactory = GeneralUtility::makeInstance(ContentBlockConfigurationFactory::class);

        // return $contentBlockConfFactory->createFromArray($cbConf);
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
            $realPath = $splPath->getPathname() . DIRECTORY_SEPARATOR;

            // composer.json
            if (!is_readable($realPath . 'composer.json')) {
                throw new \Exception(sprintf('Cannot read or find composer.json file: %s', $realPath . 'composer.json'));
            }

            $composerJson = json_decode(file_get_contents($realPath . 'composer.json'), true);

            if ($composerJson['type'] !== $this->configurationService->getComposerType()) {
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
