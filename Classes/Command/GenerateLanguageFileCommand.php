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

namespace TYPO3\CMS\ContentBlocks\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(tags: [
    [
        'name' => 'console.command',
        'command' => 'content-blocks:language:generate',
        'description' => 'Update labels.xlf for the given Content Block',
        'schedulable' => false,
    ],
])]
class GenerateLanguageFileCommand extends Command
{
    public function __construct(
        protected readonly LanguageFileGenerator $languageFileGenerator,
        protected readonly PackageManager $packageManager,
        protected readonly ContentBlockLoader $contentBlockLoader,
        protected readonly TableDefinitionCollectionFactory $tableDefinitionCollectionFactory,
        protected readonly FieldTypeRegistry $fieldTypeRegistry,
        protected readonly SimpleTcaSchemaFactory $simpleTcaSchemaFactory,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'content-block',
            InputArgument::OPTIONAL,
            'The Content Block name to generate the language file for.'
        );
        $this->addOption(
            'print',
            'p',
            InputOption::VALUE_NONE,
            'Print labels.xlf to terminal instead of writing to file system.'
        );
        $this->addOption(
            'extension',
            'e',
            InputOption::VALUE_REQUIRED,
            'Define an extension key to process all Content Blocks within.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentBlockRegistry = $this->contentBlockLoader->loadUncached();
        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $this->fieldTypeRegistry,
            $this->simpleTcaSchemaFactory,
        );
        $automaticLanguageKeysRegistry = $tableDefinitionCollection->getAutomaticLanguageKeysRegistry();
        $this->languageFileGenerator->setAutomaticLanguageKeysRegistry($automaticLanguageKeysRegistry);
        $contentBlockName = $input->getArgument('content-block');
        $print = (bool)$input->getOption('print');
        $extension = (string)$input->getOption('extension');

        if ($print && $contentBlockName === null) {
            if ($extension) {
                $output->writeln('<error>Using `--extension` for print mode is not allowed. Please provide a Content Block as first argument.</error>');
            } else {
                $output->writeln('<error>Please provide a Content Block as first argument.</error>');
            }
            return Command::INVALID;
        }
        if (!$print && $contentBlockName === null && $extension === '') {
            $output->writeln('<error>Either provide a Content Block as first argument or set --extension to process all Content Blocks within the extension.</error>');
            return Command::INVALID;
        }

        if ($print) {
            $this->printLabelsXlf($contentBlockRegistry, $contentBlockName, $output);
        } elseif ($extension !== '') {
            try {
                $this->packageManager->getPackage($extension);
            } catch (UnknownPackageException) {
                $output->writeln('<error>Extension with key "' . $extension . '" does not exist.</error>');
                return Command::INVALID;
            }
            foreach ($contentBlockRegistry->getAll() as $contentBlock) {
                if ($contentBlock->getHostExtension() !== $extension) {
                    continue;
                }
                $this->writeLabelsXlf($contentBlock);
            }
        } else {
            $contentBlock = $contentBlockRegistry->getContentBlock($contentBlockName);
            $this->writeLabelsXlf($contentBlock);
        }
        return Command::SUCCESS;
    }

    protected function writeLabelsXlf(LoadedContentBlock $contentBlock): void
    {
        $contentBlockPath = GeneralUtility::getFileAbsFileName($contentBlock->getExtPath());
        $labelsFolder = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFolder();
        $labelsXlfPath = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFilePath();
        $result = $this->languageFileGenerator->generate($contentBlock);
        GeneralUtility::mkdir_deep($labelsFolder);
        GeneralUtility::writeFile($labelsXlfPath, $result);
    }

    protected function printLabelsXlf(ContentBlockRegistry $contentBlockRegistry, string $contentBlockName, OutputInterface $output): void
    {
        $contentBlock = $contentBlockRegistry->getContentBlock($contentBlockName);
        $result = $this->languageFileGenerator->generate($contentBlock);
        $output->writeln($result, OutputInterface::OUTPUT_RAW);
    }
}
