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
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Generator\HtmlTemplateCodeGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(tags: [
    [
        'name' => 'console.command',
        'command' => 'content-blocks:generate:backend-preview',
        'description' => 'Generate a backend preview template for a Content Block',
        'schedulable' => false,
    ],
])]
class GenerateBackendPreviewTemplateCommand extends Command
{
    public function __construct(
        protected readonly HtmlTemplateCodeGenerator $htmlTemplateCodeGenerator,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly TableDefinitionCollection $tableDefinitionCollection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'content-block',
            InputArgument::REQUIRED,
            'The Content Block name to generate the backend preview template for (e.g. "vendor/name").'
        );
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Override existing backend preview template.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentBlockName = $input->getArgument('content-block');

        if (!$this->contentBlockRegistry->hasContentBlock($contentBlockName)) {
            $output->writeln('<error>Content Block "' . $contentBlockName . '" not found.</error>');
            return Command::INVALID;
        }

        $contentBlock = $this->contentBlockRegistry->getContentBlock($contentBlockName);
        $basePath = GeneralUtility::getFileAbsFileName($contentBlock->getExtPath());
        $filePath = $basePath . '/' . ContentBlockPathUtility::getBackendPreviewPath();

        if (file_exists($filePath) && !$input->getOption('force')) {
            $output->writeln('<error>Backend preview template for Content Block "' . $contentBlockName . '" already exists at "' . $filePath . '". Use --force to override.</error>');
            return Command::INVALID;
        }

        $result = $this->htmlTemplateCodeGenerator->generateEditorPreviewTemplate($contentBlock, $this->tableDefinitionCollection);
        GeneralUtility::writeFile($filePath, $result);

        return Command::SUCCESS;
    }
}
