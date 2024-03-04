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
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockBuilder;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Service\PackageResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageInterface;

class CreateContentBlockCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockBuilder $contentBlockBuilder,
        protected readonly PackageResolver $packageResolver,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'content-type',
            '',
            InputOption::VALUE_OPTIONAL,
            'Content type of Content Block. One of: ' . implode(', ', array_keys($this->getSupportedTypes())) . '.'
        );
        $this->addOption(
            'vendor',
            '',
            InputOption::VALUE_OPTIONAL,
            'Vendor of Content Block (The name must be lowercase and consist of words separated by dashes "-").'
        );
        $this->addOption(
            'name',
            '',
            InputOption::VALUE_OPTIONAL,
            'Name of Content Block (The name must be lowercase and consist of words separated by dashes "-").'
        );
        $this->addOption(
            'type-name',
            '',
            InputOption::VALUE_OPTIONAL,
            'Type identifier of Content Block. Falls back to combination of "vendor" and "name". Must be integer value for content type "page-type".'
        );
        $this->addOption(
            'extension',
            '',
            InputOption::VALUE_OPTIONAL,
            'Host extension in which the Content Block should be stored.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $typeName = $input->getOption('type-name');
        $availablePackages = $this->packageResolver->getAvailablePackages();
        if ($availablePackages === []) {
            throw new \RuntimeException('No packages were found in which to store the Content Block.', 1678699706);
        }

        if ($input->getOption('content-type')) {
            $contentTypeFromInput = $input->getOption('content-type');
        } else {
            $contentTypeFromInput = $io->askQuestion(new ChoiceQuestion('Choose the Content Type of your Content Block', $this->getSupportedTypes(), 'content-element'));
        }
        $contentType = match ($contentTypeFromInput) {
            'content-element' => ContentType::CONTENT_ELEMENT,
            'page-type' => ContentType::PAGE_TYPE,
            'record-type' => ContentType::RECORD_TYPE,
            default => throw new \RuntimeException(
                'Content type "' . $contentTypeFromInput . '" could not be found. Please choose one of these types: ' . implode(', ', array_keys($this->getSupportedTypes())),
                1678781014
            )
        };
        if ($input->getOption('vendor')) {
            $vendor = $input->getOption('vendor');
            if (!ContentBlockNameValidator::isValid($vendor)) {
                $output->writeln('<error>Your vendor name does not match the requirement.</error>');
                return Command::INVALID;
            }
        } else {
            $default = null;
            $rootVendor = $this->packageResolver->getComposerProjectVendor();
            if ($rootVendor !== '') {
                $default = $rootVendor;
            }
            $contentBlockVendorQuestion = new Question('Enter your vendor name (lowercase, separated by dashes "-")', $default);
            $contentBlockVendorQuestion->setValidator($this->validateName(...));
            while (($vendor = $io->askQuestion($contentBlockVendorQuestion)) === false) {
                $output->writeln('<error>Your vendor name does not match the requirement.</error>');
            }
        }
        $vendor = strtolower($vendor);
        if ($input->getOption('name')) {
            $name = $input->getOption('name');
            if (!ContentBlockNameValidator::isValid($name)) {
                $output->writeln('<error>Your Content Block name does not match the requirement.</error>');
                return Command::INVALID;
            }
        } else {
            $contentBlockNameQuestion = new Question('Enter your Content Block name (lowercase, separated by dashes "-")');
            $contentBlockNameQuestion->setValidator($this->validateName(...));
            while (($name = $io->askQuestion($contentBlockNameQuestion)) === false) {
                $output->writeln('<error>Your Content Block name does not match the requirement.</error>');
            }
        }
        $name = strtolower($name);
        if ($contentType === ContentType::PAGE_TYPE) {
            if ($typeName === null) {
                $currentTimeStamp = time();
                $whatIsTheTypeName = new Question('Enter a unique integer type. Press enter for current timestamp "' . $currentTimeStamp . '"');
                $typeName = $io->askQuestion($whatIsTheTypeName);
                if ($typeName === null) {
                    $typeName = $currentTimeStamp;
                }
            }
            PageTypeNameValidator::validate($typeName, $vendor . '/' . $name);
            $typeName = (int)$typeName;
        }

        $contentBlockName = $vendor . '/' . $name;
        if ($this->contentBlockRegistry->hasContentBlock($contentBlockName)) {
            $output->writeln(
                '<error>A Content Block with the name "' . $contentBlockName . '" already exists. Please run'
                . ' the command again and specify a different combination of vendor name and content block name.</error>'
            );
            return Command::INVALID;
        }

        $yamlConfiguration = match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->createContentBlockContentElementConfiguration($vendor, $name, $typeName),
            ContentType::PAGE_TYPE => $this->createContentBlockPageTypeConfiguration($vendor, $name, $typeName),
            ContentType::RECORD_TYPE => $this->createContentBlockRecordTypeConfiguration($vendor, $name, $typeName),
        };

        if ($input->getOption('extension')) {
            $extension = $input->getOption('extension');
            if (!array_key_exists($extension, $availablePackages)) {
                throw new \RuntimeException(
                    'The extension "' . $extension . '" could not be found. Please choose one of these extensions: ' . implode(', ', $this->getPackageKeys($availablePackages)),
                    1678781015
                );
            }
        } else {
            $extension = $io->askQuestion(new ChoiceQuestion('Choose an extension in which the Content Block should be stored', $this->getPackageTitles($availablePackages)));
        }

        $contentBlockConfiguration = new LoadedContentBlock(
            name: $contentBlockName,
            yaml: $yamlConfiguration,
            icon: '',
            iconProvider: '',
            hostExtension: $extension,
            extPath: $this->getExtPath($extension, $contentType),
            contentType: $contentType
        );

        $this->contentBlockBuilder->create($contentBlockConfiguration);

        $output->writeln('<info>Successfully created new Content Block "' . $vendor . '/' . $name . '" inside ' . $extension . '.</info>');
        $output->writeln('<comment>Please run the following commands now and every time you change the EditorInterface.yaml file.</comment>');
        $output->writeln('<comment>Alternatively, flush the system cache in the backend and run the Database Analyzer.</comment>');

        $command = Environment::isComposerMode() ? 'vendor/bin/typo3' : 'typo3/sysext/core/bin/typo3';
        $output->writeln($command . ' cache:flush -g system');
        $output->writeln($command . ' extension:setup --extension=' . $extension);

        return Command::SUCCESS;
    }

    protected function validateName(?string $name): string|bool
    {
        $name = (string)$name;
        if (ContentBlockNameValidator::isValid($name)) {
            return $name;
        }
        return false;
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

    protected function getExtPath(string $extension, ContentType $contentType): string
    {
        $base = 'EXT:' . $extension . '/';
        return match ($contentType) {
            ContentType::CONTENT_ELEMENT => $base . ContentBlockPathUtility::getRelativeContentElementsPath(),
            ContentType::PAGE_TYPE => $base . ContentBlockPathUtility::getRelativePageTypesPath(),
            ContentType::RECORD_TYPE => $base . ContentBlockPathUtility::getRelativeRecordTypesPath()
        };
    }

    private function createContentBlockContentElementConfiguration(string $vendor, string $name, ?string $typeName = ''): array
    {
        $fullName = $vendor . '/' . $name;
        $description = 'Description for ' . ContentType::CONTENT_ELEMENT->getHumanReadable() . ' ' . $fullName;
        $configuration = [
            'table' => 'tt_content',
            'typeField' => 'CType',
            'name' => $fullName,
            'title' => $fullName,
            'description' => $description,
            'group' => 'common',
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $configuration['fields'] = [
            [
                'identifier' => 'header',
                'useExistingField' => true,
                'label' => 'Custom header label',
            ],
        ];
        return $configuration;
    }

    private function createContentBlockPageTypeConfiguration(string $vendor, string $name, int $typeName): array
    {
        $fullName = $vendor . '/' . $name;
        return [
            'table' => 'pages',
            'typeField' => 'doktype',
            'name' => $fullName,
            'title' => $fullName,
            'typeName' => $typeName,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
    }

    private function createContentBlockRecordTypeConfiguration(string $vendor, string $name, ?string $typeName = ''): array
    {
        $fullName = $vendor . '/' . $name;
        $vendorWithoutSeparator = str_replace('-', '', $vendor);
        $nameWithoutSeparator = str_replace('-', '', $name);
        // "tx_" is prepended per default for better grouping in the New Record view.
        // Otherwise, this would be listed in "System Records".
        $table = 'tx_' . $vendorWithoutSeparator . '_' . $nameWithoutSeparator;
        $labelField = 'title';
        $configuration = [
            'name' => $fullName,
            'table' => $table,
            'title' => $fullName,
            'prefixFields' => false,
            'labelField' => $labelField,
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $configuration['fields'] = [
            [
                'identifier' => $labelField,
                'type' => 'Text',
                'label' => 'Title',
            ],
        ];
        return $configuration;
    }
}
