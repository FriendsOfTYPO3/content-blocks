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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\ContentBlocks\Definition\Factory\TableDefinitionCollectionFactory;
use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeRegistry;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Schema\SimpleTcaSchemaFactory;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand;
use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[AsCommand(
    'content-blocks:language:generate',
    'Update labels.xlf for the given Content Block',
)]
#[AsNonSchedulableCommand]
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
        $this->addOption(
            'keep-date',
            null,
            InputOption::VALUE_NONE,
            'Keep the date attribute of an existing labels.xlf instead of setting it to the current date.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentBlockName = $input->getArgument('content-block');
        $print = (bool)$input->getOption('print');
        $extension = (string)$input->getOption('extension');
        $keepDate = (bool)$input->getOption('keep-date');
        return $print
             ? $this->startPrintProcess($contentBlockName, $extension, $keepDate, $output)
             : $this->startWriteProcess($contentBlockName, $extension, $keepDate, $output);
    }

    protected function startPrintProcess(
        ?string $contentBlockName,
        string $extension,
        bool $keepDate,
        OutputInterface $output,
    ): int {
        $valid = $this->validatePrintMode($contentBlockName, $extension, $output);
        if ($valid === false) {
            return Command::INVALID;
        }
        $contentBlockRegistry = $this->loadUncachedContentBlockRegistry();
        $this->printLabelsXlf($contentBlockRegistry, $contentBlockName, $keepDate, $output);
        return Command::SUCCESS;
    }

    protected function startWriteProcess(
        ?string $contentBlockName,
        string $extension,
        bool $keepDate,
        OutputInterface $output,
    ): int {
        $valid = $this->validateWriteMode($contentBlockName, $extension, $output);
        if ($valid === false) {
            return Command::INVALID;
        }
        $contentBlockRegistry = $this->loadUncachedContentBlockRegistry();
        if ($extension !== '') {
            return $this->writeForAllInExtension($extension, $contentBlockRegistry, $keepDate, $output);
        }
        $contentBlock = $contentBlockRegistry->getContentBlock($contentBlockName);
        $this->writeLabelsXlf($contentBlock, $keepDate);
        return Command::SUCCESS;
    }

    protected function loadUncachedContentBlockRegistry(): ContentBlockRegistry
    {
        $contentBlockRegistry = $this->contentBlockLoader->loadUncached();
        $tableDefinitionCollection = $this->tableDefinitionCollectionFactory->createUncached(
            $contentBlockRegistry,
            $this->fieldTypeRegistry,
            $this->simpleTcaSchemaFactory,
        );
        $automaticLanguageKeysRegistry = $tableDefinitionCollection->getAutomaticLanguageKeysRegistry();
        $this->languageFileGenerator->setAutomaticLanguageKeysRegistry($automaticLanguageKeysRegistry);
        return $contentBlockRegistry;
    }

    protected function writeForAllInExtension(
        string $extension,
        ContentBlockRegistry $contentBlockRegistry,
        bool $keepDate,
        OutputInterface $output,
    ): int {
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
            $this->writeLabelsXlf($contentBlock, $keepDate);
        }
        return Command::SUCCESS;
    }

    protected function writeLabelsXlf(LoadedContentBlock $contentBlock, bool $keepDate): void
    {
        $contentBlockPath = GeneralUtility::getFileAbsFileName($contentBlock->getExtPath());
        $labelsFolder = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFolder();
        $labelsXlfPath = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFilePath();
        $date = $keepDate ? $this->resolveDateFromLabelsXlf($labelsXlfPath) : null;
        $result = $this->languageFileGenerator->generate($contentBlock, $date);
        // Avoid writing new labels.xlf if it is identical to the current one.
        if (file_exists($labelsXlfPath)) {
            $current = file_get_contents($labelsXlfPath);
            $currentWithoutDate = $this->removeDateFromXLF($current);
            $resultWithoutDate = $this->removeDateFromXLF($result);
            if ($currentWithoutDate === $resultWithoutDate) {
                return;
            }
        }
        GeneralUtility::mkdir_deep($labelsFolder);
        GeneralUtility::writeFile($labelsXlfPath, $result);
    }

    protected function removeDateFromXLF(string $xlf): string
    {
        $result = preg_replace('/date="(.*)"/', '', $xlf);
        return $result;
    }

    /**
     * Returns null, if the file or its date attribute does not exist, so the current date is used instead.
     */
    protected function resolveDateFromLabelsXlf(string $labelsXlfPath): ?string
    {
        if (!file_exists($labelsXlfPath)) {
            return null;
        }
        $xlf = file_get_contents($labelsXlfPath);
        if (preg_match('/date="([^"]*)"/', $xlf, $matches) !== 1) {
            return null;
        }
        $date = $matches[1];
        return $date;
    }

    protected function printLabelsXlf(
        ContentBlockRegistry $contentBlockRegistry,
        string $contentBlockName,
        bool $keepDate,
        OutputInterface $output
    ): void {
        $contentBlock = $contentBlockRegistry->getContentBlock($contentBlockName);
        $contentBlockPath = GeneralUtility::getFileAbsFileName($contentBlock->getExtPath());
        $labelsXlfPath = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFilePath();
        $date = $keepDate ? $this->resolveDateFromLabelsXlf($labelsXlfPath) : null;
        $result = $this->languageFileGenerator->generate($contentBlock, $date);
        $output->writeln($result, OutputInterface::OUTPUT_RAW);
    }

    protected function validatePrintMode(?string $contentBlockName, string $extension, OutputInterface $output): bool
    {
        if ($contentBlockName !== null) {
            return true;
        }
        if ($extension !== '') {
            $output->writeln('<error>Using `--extension` for print mode is not allowed. Please provide a Content Block as first argument.</error>');
        } else {
            $output->writeln('<error>Please provide a Content Block as first argument.</error>');
        }
        return false;
    }

    protected function validateWriteMode(?string $contentBlockName, string $extension, OutputInterface $output): bool
    {
        if ($contentBlockName === null && $extension === '') {
            $output->writeln('<error>Either provide a Content Block as first argument or set --extension to process all Content Blocks within the extension.</error>');
            return false;
        }
        return true;
    }
}
