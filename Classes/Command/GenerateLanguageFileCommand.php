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
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Package\Exception\UnknownPackageException;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GenerateLanguageFileCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly LanguageFileGenerator $languageFileGenerator,
        protected readonly PackageManager $packageManager,
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
            'Print Labels.xlf to terminal instead of writing to file system.'
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
            $this->printLabelsXlf($contentBlockName, $output);
        } else {
            if ($extension !== '') {
                try {
                    $this->packageManager->getPackage($extension);
                } catch (UnknownPackageException) {
                    $output->writeln('<error>Extension with key "' . $extension . '" does not exist.</error>');
                    return Command::INVALID;
                }
                foreach ($this->contentBlockRegistry->getAll() as $contentBlock) {
                    if ($contentBlock->getHostExtension() !== $extension) {
                        continue;
                    }
                    $this->writeLabelsXlf($contentBlock);
                }
            } else {
                $contentBlock = $this->contentBlockRegistry->getContentBlock($contentBlockName);
                $this->writeLabelsXlf($contentBlock);
            }
        }
        return Command::SUCCESS;
    }

    protected function writeLabelsXlf(LoadedContentBlock $contentBlock): void
    {
        $contentBlockPath = GeneralUtility::getFileAbsFileName($contentBlock->getExtPath());
        $labelsFolder = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFolderPath();
        $labelsXlfPath = $contentBlockPath . '/' . ContentBlockPathUtility::getLanguageFilePath();
        $result = $this->languageFileGenerator->generate($contentBlock);
        GeneralUtility::mkdir_deep($labelsFolder);
        GeneralUtility::writeFile($labelsXlfPath, $result);
    }

    protected function printLabelsXlf(string $contentBlockName, OutputInterface $output): void
    {
        $contentBlock = $this->contentBlockRegistry->getContentBlock($contentBlockName);
        $result = $this->languageFileGenerator->generate($contentBlock);
        $output->writeln($result, OutputInterface::OUTPUT_RAW);
    }
}
