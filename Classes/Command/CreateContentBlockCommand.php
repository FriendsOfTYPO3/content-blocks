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
use TYPO3\CMS\ContentBlocks\Service\CreateContentType;
use TYPO3\CMS\ContentBlocks\Service\PackageResolver;
use TYPO3\CMS\ContentBlocks\Utility\ContentBlockPathUtility;
use TYPO3\CMS\ContentBlocks\Validation\ContentBlockNameValidator;
use TYPO3\CMS\ContentBlocks\Validation\PageTypeNameValidator;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Package\PackageInterface;

class CreateContentBlockCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockSkeletonBuilder $contentBlockBuilder,
        protected readonly PackageResolver $packageResolver,
        protected readonly PageTypeNameValidator $pageTypeNameValidator,
        protected readonly CreateContentType $createContentType
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addOption('content-type', '', InputOption::VALUE_OPTIONAL, 'Content type of content block. One of: ' . implode(', ', array_keys($this->getSupportedTypes())) . '.');
        $this->addOption('vendor', '', InputOption::VALUE_OPTIONAL, 'Vendor of content block (The name must be lowercase and consist of words separated by dashes "-").');
        $this->addOption('name', '', InputOption::VALUE_OPTIONAL, 'Name of content block (The name must be lowercase and consist of words separated by dashes "-").');
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
        } else {
            $contentTypeFromInput = $io->askQuestion(new ChoiceQuestion('Choose the content type of your content block', $this->getSupportedTypes(), 'content-element'));
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
            $contentBlockVendorQuestion = new Question('Enter your vendor name (lowercase, separated by dashes "-")');
            $contentBlockVendorQuestion->setValidator($this->validateName(...));
            while (($vendor = $io->askQuestion($contentBlockVendorQuestion)) === false) {
                $output->writeln('<error>Your vendor name does not match the requirement.</error>');
            }
        }
        $vendor = strtolower($vendor);
        if ($input->getOption('name')) {
            $name = $input->getOption('name');
            if (!ContentBlockNameValidator::isValid($name)) {
                $output->writeln('<error>Your content block name does not match the requirement.</error>');
                return Command::INVALID;
            }
        } else {
            $contentBlockNameQuestion = new Question('Enter your content block name (lowercase, separated by dashes "-")');
            $contentBlockNameQuestion->setValidator($this->validateName(...));
            while (($name = $io->askQuestion($contentBlockNameQuestion)) === false) {
                $output->writeln('<error>Your content block name does not match the requirement.</error>');
            }
        }
        $name = strtolower($name);
        if ($contentType === ContentType::PAGE_TYPE) {
            if ($input->getOption('type')) {
                $type = $input->getOption('type');
            } else {
                $currentTimeStamp = time();
                $type = $io->askQuestion(new Question('Enter a unique integer type. Press enter for current timestamp "' . $currentTimeStamp . '"'));
                if ($type === null) {
                    $type = $currentTimeStamp;
                }
            }
            $this->pageTypeNameValidator->validate($type, $vendor . '/' . $name);
            $type = (int)$type;
        }

        $yamlConfiguration = match ($contentType) {
            ContentType::CONTENT_ELEMENT => $this->createContentType->createContentBlockContentElementConfiguration(
                $vendor,
                $name,
                [
                    [
                        'identifier' => 'header',
                        'useExistingField' => true,
                    ],
                ]
            ),
            ContentType::PAGE_TYPE => $this->createContentType->createContentBlockPageTypeConfiguration($vendor, $name, $type),
            ContentType::RECORD_TYPE => $this->createContentType->createContentBlockRecordTypeConfiguration($vendor, $name, $type),
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
            $extension = $io->askQuestion(new ChoiceQuestion('Choose an extension in which the content block should be stored', $this->getPackageTitles($availablePackages)));
        }

        $contentBlockConfiguration = new ContentBlockConfiguration(
            yamlConfig: $yamlConfiguration,
            basePath: $this->createContentType->getBasePath($availablePackages, $extension, $contentType),
            contentType: $contentType
        );

        $this->contentBlockBuilder->create($contentBlockConfiguration);

        $output->writeln('<info>Successfully created new Content Block "' . $vendor . '/' . $name . '" inside ' . $extension . '.</info>');
        $output->writeln('<question>Please run the following commands now and every time you change the EditorInterface.yaml file:</question>');
        $output->writeln('<question>(Or flush the system cache in the backend and run the Database Analyzer)</question>');

        $command = Environment::isComposerMode() ? 'vendor/bin/typo3' : 'typo3/sysext/core/bin/typo3';
        $output->writeln($command . ' cache:flush -g system');
        $output->writeln($command . ' extension:setup --extension=' . $extension);

        return Command::SUCCESS;
    }

    protected function validateName(string $name): string|bool
    {
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
}
