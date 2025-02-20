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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;

#[Autoconfigure(tags: [
    [
        'name' => 'console.command',
        'command' => 'content-blocks:list',
        'description' => 'List available Content Blocks',
        'schedulable' => false,
    ],
])]
class ListContentBlocksCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockRegistry $contentBlockRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $orderDescription = 'Order result by one of "vendor", "name", "table", "type-name", "content-type" or "extension".';
        $this->addOption('order', 'o', InputOption::VALUE_OPTIONAL, $orderDescription, 'vendor');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $headers = ['vendor', 'name', 'table', 'type-name', 'content-type', 'extension'];
        $order = $input->getOption('order');
        if (!in_array($order, $headers, true)) {
            $errorMessage = '<error>Order "' . $order . '" is not allowed. Please use one of ' . implode(', ', $headers) . '.</error>';
            $output->writeln($errorMessage);
            return Command::INVALID;
        }
        $availableContentBlocks = $this->getAvailableContentBlocks();

        if ($availableContentBlocks === []) {
            $output->writeln('<info>There aren\'t any content blocks on your system. Use `make:content-block` to create your first one.</info>');
            return Command::SUCCESS;
        }

        usort($availableContentBlocks, function (array $a, array $b) use ($order): int {
            if ($order === 'content-type') {
                return $a[$order] <=> $b[$order];
            }
            return [$a[$order], $a['content-type']] <=> [$b[$order], $b['content-type']];
        });
        $table = new Table($output);
        $table->setHeaders($headers);
        $table->setRows($availableContentBlocks);
        $table->render();
        return Command::SUCCESS;
    }

    protected function getAvailableContentBlocks(): array
    {
        $list = [];
        foreach ($this->contentBlockRegistry->getAll() as $loadedContentBlock) {
            $table = match ($loadedContentBlock->getContentType()) {
                ContentType::RECORD_TYPE => $loadedContentBlock->getYaml()['table'],
                default => $loadedContentBlock->getContentType()->getTable(),
            };
            $typeName = $loadedContentBlock->getYaml()['typeName'];
            $list[] = [
                'vendor' => $loadedContentBlock->getVendor(),
                'name' => $loadedContentBlock->getPackage(),
                'table' => $table,
                'type-name' => $typeName,
                'content-type' => $loadedContentBlock->getContentType()->getHumanReadable(),
                'extension' => $loadedContentBlock->getHostExtension(),
            ];
        }
        return $list;
    }
}
