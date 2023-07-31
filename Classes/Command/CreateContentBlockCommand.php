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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockSkeletonBuilder;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Utility\MathUtility;

class CreateContentBlockCommand extends Command
{
    /** @var list<int> $reservedPageTypes */
    protected array $reservedPageTypes = [
        PageRepository::DOKTYPE_DEFAULT,
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
        PageRepository::DOKTYPE_BE_USER_SECTION,
        PageRepository::DOKTYPE_SPACER,
        PageRepository::DOKTYPE_SYSFOLDER
    ];

    public function __construct(
        protected readonly ContentBlockSkeletonBuilder $contentBlockBuilder,
        protected readonly PackageResolver $packageResolver
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addOption('type', '', InputOption::VALUE_OPTIONAL, 'The type of the content block.');
        $this->addOption('type-name', '', InputOption::VALUE_OPTIONAL, 'The typeName of the content block (only necessary for PageTypes).');
        $this->addOption('vendor', '', InputOption::VALUE_OPTIONAL, 'The vendor of the content block.');
        $this->addOption('name', '', InputOption::VALUE_OPTIONAL, 'The name of the content block.');
        $this->addOption('extension', '', InputOption::VALUE_OPTIONAL, 'Enter extension in which the content block should be stored.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $availablePackages = $this->packageResolver->getAvailablePackages();
        if ($availablePackages === []) {
            throw new \RuntimeException('No packages were found in which to store the content block.', 1678699706);
        }

        /** @var QuestionHelper $questionHelper */
        if ($input->getOption('type')) {
            $type = $input->getOption('type');
            if (!array_key_exists($type, $this->getSupportedTypes())) {
                throw new \RuntimeException(
                    'Type "' . $type . '" could not be found. Please choose one of these types: ' . implode(', ', array_keys($this->getSupportedTypes())),
                    1678781014
                );
            }
        } else {
            $io = new SymfonyStyle($input, $output);
            $type = $io->askQuestion(new ChoiceQuestion('Choose the type of your content block', $this->getSupportedTypes(), 'content-element'));
        }
        $questionHelper = $this->getHelper('question');
        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        } else {
            $questionVendor = new Question('Enter your vendor name: ');
            $vendor = $questionHelper->ask($input, $output, $questionVendor);
        }
        if ($input->getOption('name')) {
            $name = $input->getOption('name');
        } else {
            $questionPackage = new Question('Enter your content block name: ');
            $name = $questionHelper->ask($input, $output, $questionPackage);
        }
        if ($input->getOption('extension')) {
            $extension = $input->getOption('extension');
            if (!array_key_exists($extension, $availablePackages)) {
                throw new \RuntimeException(
                    'The extension "' . $extension . '" could not be found. Please choose one of these extensions: ' . implode(', ', $this->getPackageKeys($availablePackages)),
                    1678781015
                );
            }
        } else {
            $io = new SymfonyStyle($input, $output);
            $extension = $io->askQuestion(new ChoiceQuestion('Choose an extension in which the content block should be stored', $this->getPackageTitles($availablePackages)));
        }
        if ($type === 'page-type') {
            if ($input->getOption('type-name')) {
                $typeName = (int) $input->getOption('type-name');
                if (!MathUtility::canBeInterpretedAsInteger($typeName) || $typeName < 0 || in_array($typeName, $this->reservedPageTypes)) {
                    throw new \InvalidArgumentException(
                        'Invalid value "' . $typeName . '" for "typeName" in ContentBlock "' . $name . '". Value must be a positive integer and not one of the reserved page types: '
                        . implode(', ', $this->reservedPageTypes),
                        1689287031
                    );
                }
            } else {
                $io = new SymfonyStyle($input, $output);
                $typeName = (int)$io->askQuestion(new Question('Enter a unique integer type name '));
            }
            $yamlConfiguration = $this->createContentBlockPageTypeConfiguration($vendor, $name, $typeName);
        } elseif ($type === 'content-element') {
            $yamlConfiguration = $this->createContentBlockContentElementConfiguration($vendor, $name);
        } else {
            $yamlConfiguration = $this->createContentBlockRecordTypeConfiguration($vendor, $name);
        }
        $contentBlockConfiguration = new ContentBlockConfiguration(
            yamlConfig: $yamlConfiguration,
            basePath: $this->getBasePath($availablePackages, $extension, $type)
        );

        $this->contentBlockBuilder->create($contentBlockConfiguration);

        return Command::SUCCESS;
    }

    /**
     * @param array<string, PackageInterface> $availablePackages
     * @return array<string, string>
     */
    protected function getPackageTitles(array $availablePackages): array
    {
        return array_map(fn(PackageInterface $package): string => $package->getPackageMetaData()->getTitle(), $availablePackages);
    }

    /**
     * @param array<string, PackageInterface> $availablePackages
     * @return array<string, string>
     */
    protected function getPackageKeys(array $availablePackages): array
    {
        return array_map(fn(PackageInterface $package): string => $package->getPackageKey(), $availablePackages);
    }

    /**
     * @return array<string, string>
     */
    protected function getSupportedTypes(): array
    {
        return ['content-element' => 'Content Element', 'page-type' => 'Page Type', 'record-type' => 'Record Type'];
    }

    /**
     * @param array $availablePackages
     * @param string $extension
     * @param string $type
     * @return string
     */
    protected function getBasePath(array $availablePackages, string $extension, string $type): string
    {
        return match ($type) {
            'content-element' => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeContentElementsPath(),
            'page-type' => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativePageTypesPath(),
            'record-type' => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeRecordTypesPath()
        };
    }

    private function createContentBlockContentElementConfiguration(string $vendor, string $name): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'group' => 'common',
            'prefixFields' => true,
            'fields' => [
                [
                    'identifier' => 'header',
                    'useExistingField' => true,
                ],
            ],
        ];
    }

    private function createContentBlockPageTypeConfiguration(string $vendor, string $name, int $typeName): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'typeName' => $typeName,
            'prefixFields' => true,
            'fields' => [],
        ];
    }

    private function createContentBlockRecordTypeConfiguration(string $vendor, string $name): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'table' => 'tx_' . $vendor . '_domain_model_' . $name,
            'prefixFields' => false,
            'useAsLabel' => 'title',
            'fields' => [
                [
                    'identifier' => 'title',
                    'type' => 'Text'
                ],
            ],
        ];
    }
}
