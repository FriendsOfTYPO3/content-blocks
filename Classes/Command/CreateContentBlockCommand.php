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
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockSkeletonBuilder;
use TYPO3\CMS\Core\Core\Environment;

class CreateContentBlockCommand extends Command
{
    protected ContentBlockSkeletonBuilder $contentBlockBuilder;

    public function injectContentBlockBuilder(ContentBlockSkeletonBuilder $contentBlockBuilder)
    {
        $this->contentBlockBuilder = $contentBlockBuilder;
    }

    public function configure()
    {
        $this->addOption('vendor', '', InputOption::VALUE_OPTIONAL, 'The vendor name of the content block.');
        $this->addOption('package', '', InputOption::VALUE_OPTIONAL, 'The package name of the content block.');
        $this->addOption('path', '', InputOption::VALUE_OPTIONAL, 'Relative project path for content block packages.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        } else {
            $questionVendor = new Question('Enter your vendor name: ');
            $vendor = $questionHelper->ask($input, $output, $questionVendor);
        }
        if ($input->getOption('package')) {
            $package = $input->getOption('package');
        } else {
            $questionPackage = new Question('Enter your package name: ');
            $package = $questionHelper->ask($input, $output, $questionPackage);
        }
        $basePath = '';
        if (Environment::isComposerMode()) {
            if ($input->getOption('path')) {
                $basePath = $input->getOption('path');
            } else {
                $defaultPath = '{publicDir}/typo3conf/content-blocks';
                $questionBasePath = new Question('Enter your relative path (Default is ' . $defaultPath . '): ');
                $basePath = $questionHelper->ask($input, $output, $questionBasePath);
            }
        }

        $composerJson = [
            'name' => $vendor . '/' . $package,
            'description' => 'This is an empty skeleton to kickstart a new content block',
            'type' => 'typo3-content-block',
            'license' => 'GPL-2.0-or-later',
        ];
        $contentBlockConfiguration = new ContentBlockConfiguration(
            composerJson: $composerJson,
            yamlConfig: [
                'group' => 'common',
                'fields' => [
                    [
                        'identifier' => 'header',
                        'type' => 'Text',
                        'useExistingField' => true,
                    ]
                ],
            ],
            basePath: (string)$basePath
        );
        $this->contentBlockBuilder->create($contentBlockConfiguration);

        return Command::SUCCESS;
    }
}
