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

use Opis\JsonSchema\ValidationResult;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\ContentBlockValidator;
use TYPO3\CMS\ContentBlocks\JsonSchemaValidation\JsonSchemaErrorFormatter;
use TYPO3\CMS\ContentBlocks\Loader\ContentBlockLoader;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand;

#[AsCommand(
    'content-blocks:lint',
    'Lint all Content Blocks against JSON Schema',
)]
#[AsNonSchedulableCommand]
class LintContentBlocksCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockLoader $contentBlockLoader,
        protected readonly ContentBlockValidator $contentBlockValidator,
        protected readonly JsonSchemaErrorFormatter $errorFormatter,
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $symfonyStyle = new SymfonyStyle($input, $output);
        $numberOfErrors = 0;
        foreach ($this->contentBlockLoader->loadUncached()->getAll() as $contentBlock) {
            $validationResult = $this->contentBlockValidator->validate($contentBlock);
            if ($validationResult->hasError() === false) {
                continue;
            }
            $numberOfErrors++;
            $this->renderError($validationResult, $contentBlock, $output);
        }
        $this->renderEndResult($symfonyStyle, $numberOfErrors);
        if ($numberOfErrors > 0) {
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    protected function renderEndResult(SymfonyStyle $symfonyStyle, int $numberOfErrors): void
    {
        if ($numberOfErrors > 0) {
            $symfonyStyle->error('Found ' . $numberOfErrors . ' errors');
            return;
        }
        $symfonyStyle->success('No errors found');
    }

    protected function renderError(ValidationResult $validationResult, LoadedContentBlock $contentBlock, OutputInterface $output): void
    {
        $flatArray = $this->gatherErrors($validationResult);
        $header = $contentBlock->getName() . ' | EXT:' . $contentBlock->getHostExtension();
        $table = new Table($output);
        $table->setHeaders(['Path', $header]);
        $table->setRows($flatArray);
        $table->render();
    }

    /**
     * @return array<array{0: string, 1: string}>
     */
    protected function gatherErrors(ValidationResult $validationResult): array
    {
        $errors = $this->errorFormatter->format($validationResult);
        $flatArray = [];
        foreach ($errors as $path => $errorItem) {
            foreach ($errorItem as $errorItemError) {
                foreach (array_keys($flatArray) as $key) {
                    // Ignore false positives: https://github.com/opis/json-schema/issues/148
                    if (str_starts_with($key, $path)) {
                        continue 2;
                    }
                }
                $flatArray[$path] = [$path, $errorItemError];
            }
        }
        $flatArray = array_values($flatArray);
        return $flatArray;
    }
}
