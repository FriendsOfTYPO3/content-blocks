<?php

declare(strict_types=1);

namespace TYPO3\CMS\ContentBlocks\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockSkeletonBuilder;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockConfiguration;

class CreateContentBlockCommand extends Command
{
    protected ContentBlockSkeletonBuilder $contentBlockBuilder;

    public function injectContentBlockBuilder(ContentBlockSkeletonBuilder $contentBlockBuilder)
    {
        $this->contentBlockBuilder = $contentBlockBuilder;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $questionVendor = new Question('Enter your vendor name: ');
        $questionPackage = new Question('Enter your package name: ');

        $vendor = $questionHelper->ask($input, $output, $questionVendor);
        $package = $questionHelper->ask($input, $output, $questionPackage);

        $composerJson = [
            'name' => $vendor . '/' . $package,
            'description' => 'This is an empty skeleton to kickstart a new content block',
            'type' => 'typo3-contentblock',
            'license' => 'GPL-2.0-or-later',
        ];
        $contentBlockConfiguration = new ContentBlockConfiguration(
            composerJson: $composerJson,
            yamlConfig: [
                'group' => 'common',
                'fields' => [],
            ]
        );
        $this->contentBlockBuilder->create($contentBlockConfiguration);

        return Command::SUCCESS;
    }
}
