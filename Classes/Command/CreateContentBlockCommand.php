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
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\ContentBlocks\Builder\ContentBlockBuilder;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeIcon;
use TYPO3\CMS\ContentBlocks\Definition\Factory\UniqueIdentifierCreator;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\ContentBlocks\Service\PackageResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageInterface;
use TYPO3\CMS\Core\Resource\FileType;
use TYPO3\CMS\Core\Utility\GeneralUtility;

#[Autoconfigure(tags: [
    [
        'name' => 'console.command',
        'command' => 'content-blocks:create',
        'description' => 'Create a Content Block',
        'schedulable' => false,
    ],
    [
        'name' => 'console.command',
        'command' => 'make:content-block',
        'schedulable' => false,
    ],
])]
class CreateContentBlockCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockBuilder $contentBlockBuilder,
        protected readonly PackageResolver $packageResolver,
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly CacheManager $cacheManager,
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
            'title',
            '',
            InputOption::VALUE_OPTIONAL,
            'Human-readable title of Content Block.'
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
        $this->addOption(
            'skeleton-path',
            '',
            InputOption::VALUE_OPTIONAL,
            'A folder which contains a basic skeleton for one or more content types.',
        );
        $this->addOption(
            'config-path',
            '',
            InputOption::VALUE_OPTIONAL,
            'A path to a yaml config file for this command. Default is content-blocks.yaml in the current directory.',
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
        $configPath = $input->getOption('config-path');
        $defaults = $this->loadDefaultsFromContentBlocksConfig($configPath, $output);

        if ($input->getOption('content-type')) {
            $contentTypeFromInput = $input->getOption('content-type');
        } else {
            $contentTypeFromInput = $io->askQuestion(new ChoiceQuestion('Choose the Content Type of your Content Block', $this->getSupportedTypes(), $defaults['content-type']));
        }
        $contentType = ContentType::from($contentTypeFromInput);
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
            $default = $defaults['vendor'] ?? $default;
            $contentBlockVendorQuestion = new Question('Define vendor (<comment>vendor-name</comment>/content-block-name)', $default);
            $contentBlockVendorQuestion->setValidator($this->validateName(...));
            while (($vendor = $io->askQuestion($contentBlockVendorQuestion)) === false) {
                $output->writeln('<error>Your vendor name does not match the requirements.</error>');
            }
        }
        $vendor = strtolower($vendor);
        if ($input->getOption('name')) {
            $name = $input->getOption('name');
            if (!ContentBlockNameValidator::isValid($name)) {
                $output->writeln('<error>Your Content Block name does not match the requirements.</error>');
                return Command::INVALID;
            }
        } else {
            $contentBlockNameQuestion = new Question('Define name (' . $vendor . '/<comment>content-block-name</comment>)');
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
        if ($contentType === ContentType::FILE_TYPE) {
            $choices = ['text', 'image', 'audio', 'video', 'application'];
            if ($typeName === null) {
                $whatIsTheTypeName = new ChoiceQuestion('Choose a file type.', $choices, 'image');
                $typeName = $io->askQuestion($whatIsTheTypeName);
            }
            $result = FileType::tryFromMimeType($typeName);
            if ($result === FileType::UNKNOWN) {
                throw new \InvalidArgumentException(
                    'Please choose a valid file type. Valid types are: ' . implode(', ', $choices),
                    1734180384
                );
            }
        }

        $contentBlockName = $vendor . '/' . $name;
        if ($this->contentBlockRegistry->hasContentBlock($contentBlockName)) {
            $output->writeln(
                '<error>A Content Block with the name "' . $contentBlockName . '" already exists. Please run'
                . ' the command again and specify a different combination of vendor name and content block name.</error>'
            );
            return Command::INVALID;
        }

        $title = $input->getOption('title');
        if ($contentType !== ContentType::FILE_TYPE && $title === null) {
            $defaultTitle = $vendor . '/' . $name;
            $question = new Question('Define title', $defaultTitle);
            $title = $io->askQuestion($question);
        }

        $yamlConfiguration = match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->createContentBlockContentElementConfiguration($vendor, $name, $title, $typeName),
            ContentType::PAGE_TYPE => $this->createContentBlockPageTypeConfiguration($vendor, $name, $title, $typeName),
            ContentType::RECORD_TYPE => $this->createContentBlockRecordTypeConfiguration($vendor, $name, $title, $typeName),
            ContentType::FILE_TYPE => $this->createContentBlockFileTypeConfiguration($vendor, $name, $typeName),
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
            $extension = $io->askQuestion(new ChoiceQuestion('Choose an extension in which the Content Block should be stored', $this->getPackageTitles($availablePackages), $defaults['extension']));
        }

        $contentBlockConfiguration = new LoadedContentBlock(
            name: $contentBlockName,
            yaml: $yamlConfiguration,
            icon: new ContentTypeIcon(),
            hostExtension: $extension,
            extPath: $this->getExtPath($extension, $contentType),
            contentType: $contentType
        );

        $skeletonPath = $input->getOption('skeleton-path') ?? $defaults['skeleton-path'];
        $skeletonPath = rtrim($skeletonPath, '/');
        $skeletonPath = getcwd() . '/' . $skeletonPath;
        $contentTypeFolderName = $contentType->value;
        $skeletonPath .= '/' . $contentTypeFolderName;
        $skeletonPath = GeneralUtility::fixWindowsFilePath($skeletonPath);
        $this->contentBlockBuilder->create($contentBlockConfiguration, $skeletonPath);

        $output->writeln('<info>Successfully created new Content Block "' . $vendor . '/' . $name . '" inside ' . $extension . '.</info>');
        $output->writeln('<comment>Please run the following commands every time you change the config.yaml file.</comment>');
        $output->writeln('<comment>Alternatively, flush the system cache in the backend and run the Database Analyzer.</comment>');

        // Flush system cache to make the new content block available in the system
        $this->cacheManager->flushCachesInGroup('system');

        // TypoScript cache needs to be flushed to enable the new CType for the frontend
        // @todo Core should define "typoscript" as a system cache
        $this->cacheManager->getCache('typoscript')->flush();

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
        $supportedTypes = [];
        foreach (ContentType::cases() as $contentType) {
            $supportedTypes[$contentType->value] = $contentType->getHumanReadable();
        }
        return $supportedTypes;
    }

    protected function getExtPath(string $extension, ContentType $contentType): string
    {
        $base = 'EXT:' . $extension . '/';
        return match ($contentType) {
            ContentType::CONTENT_ELEMENT => $base . ContentBlockPathUtility::getRelativeContentElementsPath(),
            ContentType::PAGE_TYPE => $base . ContentBlockPathUtility::getRelativePageTypesPath(),
            ContentType::RECORD_TYPE => $base . ContentBlockPathUtility::getRelativeRecordTypesPath(),
            ContentType::FILE_TYPE => $base . ContentBlockPathUtility::getRelativeFileTypesPath(),
        };
    }

    /**
     * @return array{
     *     content-type: string,
     *     vendor: ?string,
     *     skeleton-path: string,
     *     extension: ?string
     * }
     */
    private function loadDefaultsFromContentBlocksConfig(?string $configPath, OutputInterface $output): array
    {
        $config = [
            'content-type' => 'content-element',
            'vendor' => null,
            'skeleton-path' => 'content-blocks-skeleton',
            'extension' => null,
        ];
        $currentDirectory = getcwd();
        if ($currentDirectory === false) {
            return $config;
        }
        $configFile = $configPath ?? 'content-blocks.yaml';
        $path = $currentDirectory . '/' . $configFile;
        $path = GeneralUtility::fixWindowsFilePath($path);
        if (!file_exists($path)) {
            return $config;
        }
        try {
            $yaml = Yaml::parseFile($path);
        } catch (ParseException $e) {
            $output->writeln('<bg=yellow;options=bold>Warning: Error occurred parsing default config in "' . $configFile . '".</>');
            $output->writeln('<bg=yellow;options=bold>Message: ' . $e->getMessage() . '</>');
            return $config;
        }
        if (!is_array($yaml)) {
            $output->writeln('<bg=yellow;options=bold>Warning: Expected default config to be array in "' . $configFile . '".</>');
        }
        foreach (array_keys($config) as $key) {
            $config[$key] = $yaml[$key] ?? $config[$key];
        }
        return $config;
    }

    private function createContentBlockContentElementConfiguration(string $vendor, string $name, string $title, ?string $typeName = ''): array
    {
        $fullName = $vendor . '/' . $name;
        $description = 'Description for ' . ContentType::CONTENT_ELEMENT->getHumanReadable() . ' ' . $fullName;
        $configuration = [
            'table' => 'tt_content',
            'typeField' => 'CType',
            'name' => $fullName,
            'typeName' => UniqueIdentifierCreator::createContentTypeIdentifier($fullName),
            'title' => $title,
            'description' => $description,
            'group' => 'default',
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
            ],
        ];
        return $configuration;
    }

    private function createContentBlockPageTypeConfiguration(string $vendor, string $name, string $title, int $typeName): array
    {
        $fullName = $vendor . '/' . $name;
        return [
            'table' => 'pages',
            'typeField' => 'doktype',
            'name' => $fullName,
            'title' => $title,
            'typeName' => $typeName,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
    }

    private function createContentBlockRecordTypeConfiguration(string $vendor, string $name, string $title, ?string $typeName = ''): array
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
            'title' => $title,
            'prefixFields' => false,
            'labelField' => $labelField,
            'security' => [
                'ignorePageTypeRestriction' => true,
            ],
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

    private function createContentBlockFileTypeConfiguration(string $vendor, string $name, ?string $typeName = ''): array
    {
        $fullName = $vendor . '/' . $name;
        $configuration = [
            'name' => $fullName,
            'table' => 'sys_file_reference',
        ];
        if ($typeName !== '' && $typeName !== null) {
            $configuration['typeName'] = $typeName;
        }
        $configuration['fields'] = [
            [
                'identifier' => 'image_overlay_palette',
                'type' => 'Palette',
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette',
                'fields' => [
                    [
                        'identifier' => 'alternative',
                        'useExistingField' => true,
                    ],
                    [
                        'identifier' => 'description',
                        'useExistingField' => true,
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'link',
                        'useExistingField' => true,
                    ],
                    [
                        'identifier' => 'title',
                        'useExistingField' => true,
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'example_custom_field',
                        'type' => 'Text',
                        'label' => 'My custom Field',
                    ],
                    [
                        'type' => 'Linebreak',
                    ],
                    [
                        'identifier' => 'crop',
                        'useExistingField' => true,
                    ],
                ],
            ],
        ];
        return $configuration;
    }
}
