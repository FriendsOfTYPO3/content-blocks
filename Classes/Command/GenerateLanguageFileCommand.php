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
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\ContentBlocks\Generator\LanguageFileGenerator;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;

class GenerateLanguageFileCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly LanguageFileGenerator $languageFileGenerator,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('content-block', InputArgument::REQUIRED, 'The Content Block name to generate the language file for.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentBlockName = $input->getArgument('content-block');
        $contentBlock = $this->contentBlockRegistry->getContentBlock($contentBlockName);
        $result = $this->languageFileGenerator->generate($contentBlock);
        $output->writeln($result, OutputInterface::OUTPUT_RAW);
        return Command::SUCCESS;
    }
}
