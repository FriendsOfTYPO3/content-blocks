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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockSkeletonBuilder;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Package\PackageInterface;

class CreateContentBlockCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockSkeletonBuilder $contentBlockBuilder,
        protected readonly PackageResolver $packageResolver,
        protected readonly PageTypeNameValidator $pageTypeNameValidator,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addOption('content-type', '', InputOption::VALUE_OPTIONAL, 'Content type of content block. One of: ' . implode(', ', array_keys($this->getSupportedTypes())) . '.');
        $this->addOption('vendor', '', InputOption::VALUE_OPTIONAL, 'Vendor of content block (lowercase).');
        $this->addOption('name', '', InputOption::VALUE_OPTIONAL, 'Name of content block (lowercase).');
        $this->addOption('type', '', InputOption::VALUE_OPTIONAL, 'Type identifier of content block. Falls back to combination of "vendor" and "name". Must be integer value for content type "page-type".');
        $this->addOption('extension', '', InputOption::VALUE_OPTIONAL, 'Host extension in which the content block should be stored.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getOption('type');
        $availablePackages = $this->packageResolver->getAvailablePackages();
        if ($availablePackages === []) {
            throw new \RuntimeException('No packages were found in which to store the content block.', 1678699706);
        }

        if ($input->getOption('content-type')) {
            $contentTypeFromInput = $input->getOption('content-type');
            if (!array_key_exists($contentTypeFromInput, $this->getSupportedTypes())) {
                throw new \RuntimeException(
                    'Content type "' . $contentTypeFromInput . '" could not be found. Please choose one of these types: ' . implode(', ', array_keys($this->getSupportedTypes())),
                    1678781014
                );
            }
        } else {
            $contentTypeFromInput = $io->askQuestion(new ChoiceQuestion('Choose the content type of your content block', $this->getSupportedTypes(), 'content-element'));
        }
        $contentType = match ($contentTypeFromInput) {
            'content-element' => ContentType::CONTENT_ELEMENT,
            'page-type' => ContentType::PAGE_TYPE,
            default => ContentType::RECORD_TYPE
        };
        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
        } else {
            $vendor = $io->askQuestion(new Question('Enter your vendor name'));
        }
        $vendor = strtolower($vendor);
        if ($input->getOption('name')) {
            $name = $input->getOption('name');
        } else {
            $name = $io->askQuestion(new Question('Enter your content block name'));
        }
        $name = strtolower($name);
        if ($contentType === ContentType::PAGE_TYPE) {
            if ($input->getOption('type')) {
                $type = $input->getOption('type');
            } else {
                $type = $io->askQuestion(new Question('Enter a unique integer type'));
            }
            $this->pageTypeNameValidator->validate($type, $vendor . '/' . $name);
            $yamlConfiguration = $this->createContentBlockPageTypeConfiguration($vendor, $name, (int)$type);
        } elseif ($contentType === ContentType::CONTENT_ELEMENT) {
            $yamlConfiguration = $this->createContentBlockContentElementConfiguration($vendor, $name, $type);
        } else {
            $yamlConfiguration = $this->createContentBlockRecordTypeConfiguration($vendor, $name, $type);
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
            $extension = $io->askQuestion(new ChoiceQuestion('Choose an extension in which the content block should be stored', $this->getPackageTitles($availablePackages)));
        }

        $contentBlockConfiguration = new ContentBlockConfiguration(
            yamlConfig: $yamlConfiguration,
            basePath: $this->getBasePath($availablePackages, $extension, $contentType),
            contentType: $contentType
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
        return array_map(fn (PackageInterface $package): string => $package->getPackageMetaData()->getTitle(), $availablePackages);
    }

    /**
     * @param array<string, PackageInterface> $availablePackages
     * @return array<string, string>
     */
    protected function getPackageKeys(array $availablePackages): array
    {
        return array_map(fn (PackageInterface $package): string => $package->getPackageKey(), $availablePackages);
    }

    /**
     * @return array<string, string>
     */
    protected function getSupportedTypes(): array
    {
        return ['content-element' => 'Content Element', 'page-type' => 'Page Type', 'record-type' => 'Record Type'];
    }

    protected function getBasePath(array $availablePackages, string $extension, ContentType $contentType): string
    {
        return match ($contentType) {
            ContentType::CONTENT_ELEMENT => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeContentElementsPath(),
            ContentType::PAGE_TYPE => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativePageTypesPath(),
            default => $availablePackages[$extension]->getPackagePath() . ContentBlockPathUtility::getRelativeRecordTypesPath()
        };
    }

    private function createContentBlockContentElementConfiguration(string $vendor, string $name, ?string $type = ''): array
    {
        $configuration = [
            'name' => $vendor . '/' . $name,
            'group' => 'common',
            'prefixFields' => true,
        ];
        if ($type !== '' && $type !== null) {
            $configuration['typeName'] = $type;
        }
        $configuration['fields'] = [
            [
                'identifier' => 'header',
                'useExistingField' => true,
            ],
        ];
        return $configuration;
    }

    private function createContentBlockPageTypeConfiguration(string $vendor, string $name, int $type): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'typeName' => $type,
            'prefixFields' => true,
            'fields' => [],
        ];
    }

    private function createContentBlockRecordTypeConfiguration(string $vendor, string $name, ?string $type = ''): array
    {
        $configuration = [
            'name' => $vendor . '/' . $name,
            'table' => 'tx_' . $vendor . '_domain_model_' . $name,
            'prefixFields' => false,
            'useAsLabel' => 'title',
        ];
        if ($type !== '' && $type !== null) {
            $configuration['typeName'] = $type;
        }
        $configuration['fields'] = [
            [
                'identifier' => 'title',
                'type' => 'Text',
            ],
        ];
        return $configuration;
    }
}
