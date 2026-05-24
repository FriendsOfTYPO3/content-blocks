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

namespace TYPO3\CMS\ContentBlocks\Generator;

use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentType;
use TYPO3\CMS\ContentBlocks\Definition\ContentType\ContentTypeInterface;
use TYPO3\CMS\ContentBlocks\Definition\TableDefinitionCollection;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Loader\LoadedContentBlock;

/**
 * @internal Not part of TYPO3's public API.
 */
readonly class HtmlTemplateCodeGenerator
{
    public function generateEditorPreviewTemplate(LoadedContentBlock $contentBlock, TableDefinitionCollection $tableDefinitionCollection): string
    {
        $defaultContent = match ($contentBlock->getContentType()) {
            ContentType::PAGE_TYPE => $this->createPagePreview(),
            ContentType::CONTENT_ELEMENT => $this->createElementPreview($contentBlock, $tableDefinitionCollection),
            default => throw new \RuntimeException('Preview generation not implemented for Content Type ' . $contentBlock->getContentType()->value),
        };
        $defaultContentString = implode("\n", $defaultContent);

        return $defaultContentString;
    }

    public function generateFrontendTemplate(LoadedContentBlock $contentBlock, TableDefinitionCollection $tableDefinitionCollection): string
    {
        $typeName = $contentBlock->getYaml()['typeName'];
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition($typeName);
        $frontendTemplate[] = '<html';
        $frontendTemplate[] = '    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"';
        $frontendTemplate[] = '    xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"';
        $frontendTemplate[] = '    data-namespace-typo3-fluid="true"';
        $frontendTemplate[] = '>';
        $frontendTemplate[] = '';
        $frontendTemplate[] = 'Frontend template for Content Block: ' . $contentElementDefinition->getName() . '<br>';
        $frontendTemplate = array_merge($frontendTemplate, $this->createFieldVariables($contentElementDefinition, $tableDefinitionCollection, false));
        $frontendTemplate[] = '</html>';
        $frontendTemplateString = implode("\n", $frontendTemplate);

        return $frontendTemplateString;
    }

    /**
     * @return array<string>
     */
    private function createFieldVariables(ContentTypeInterface $contentType, TableDefinitionCollection $tableDefinitionCollection, bool $isPreview, int $depth = 0, string $variable = 'data'): array
    {
        $lines = [];
        foreach ($contentType->getOverrideColumns() as $column) {
            $identifier = $column->identifier;
            if ($isPreview && ($column->fieldType->getName() === 'Relation' || $column->fieldType->getName() === 'Collection')) {
                $lines[] = [
                    '<f:render partial="PageLayout/Grid" arguments="{data: data, identifier: \'' . $identifier . '\'}"/>',
                ];
                continue;
            }
            $lines[] = match ($column->fieldType->getName()) {
                'Text',
                'Email',
                'Number',
                'Color',
                'Uuid',
                'Slug',
                'SelectText',
                'SelectNumber',
                'Radio',
                'Checkbox',
                'Language' => [
                    '{' . $variable . '.' . $identifier . '}',
                ],
                'Textarea' => match ($column->getTca()['config']['enableRichtext'] ?? null) {
                    true => [
                        '<f:format.raw>{' . $variable . '.' . $identifier . '}</f:format.raw>',
                    ],
                    default => [
                        '<f:format.nl2br>{' . $variable . '.' . $identifier . '}</f:format.nl2br>',
                    ],
                },
                'DateTime' => [
                    '<f:format.date>{' . $variable . '.' . $identifier . '}</f:format.date>',
                ],
                'Link' => [
                    '<f:link.typolink parameter="{' . $variable . '.' . $identifier . '}">Link</f:link.typolink>',
                ],
                'File' => match ($column->getTca()['config']['relationship'] ?? null) {
                    'oneToOne' => [
                        '<f:image image="{' . $variable . '.' . $identifier . '}" />',
                    ],
                    default => [
                        '<f:for each="{' . $variable . '.' . $identifier . '}" as="file">',
                        '    <f:image image="{file}" />',
                        '</f:for>',
                    ],
                },
                'Category' => [
                    '<f:for each="{' . $variable . '.' . $identifier . '}" as="' . $identifier . '_item">',
                    '    {' . $identifier . '_item.title}',
                    '</f:for>',
                ],
                'Folder' => [
                    '<f:for each="{' . $variable . '.' . $identifier . '}" as="' . $identifier . '_item">',
                    '    {' . $identifier . '_item.name}',
                    '</f:for>',
                ],
                'Relation' => match ($column->getTca()['config']['relationship'] ?? null) {
                    'oneToOne',
                    'manyToOne' => [...$this->createCollectionPreview($tableDefinitionCollection, $column, $depth, $identifier . '_item')],
                    default => [
                        '<f:for each="{' . $variable . '.' . $identifier . '}" as="' . $identifier . '_item">',
                        ...array_map(
                            fn(string $item): string => '    ' . $item,
                            $this->createCollectionPreview($tableDefinitionCollection, $column, $depth, $identifier . '_item')
                        ),
                        '</f:for>',
                    ]
                },
                'Collection' => [
                    '<f:for each="{' . $variable . '.' . $identifier . '}" as="' . $identifier . '_item">',
                    ...array_map(
                        fn(string $item): string => '    ' . $item,
                        $this->createCollectionPreview($tableDefinitionCollection, $column, $depth, $identifier . '_item')
                    ),
                    '</f:for>',
                ],
                'Select',
                'FlexForm',
                'Json' => [
                    '<f:comment>{' . $variable . '.' . $identifier . '}</f:comment>',
                ],
                'Password',
                'ImageManipulation',
                'Pass' => [],
                default => ['{' . $variable . '.' . $identifier . '}'],
            };
        }
        $frontendTemplate = array_merge([], ...$lines);
        return $frontendTemplate;
    }

    /**
     * @return array<string>
     */
    protected function createCollectionPreview(TableDefinitionCollection $tableDefinitionCollection, TcaFieldDefinition $tcaFieldDefinition, int $depth, string $identifier): array
    {
        $tca = $tcaFieldDefinition->getTca();
        $table = $tca['config']['foreign_table'] ?? $tca['config']['allowed'];
        if ($tableDefinitionCollection->hasTable($table) === false) {
            return ['{' . $identifier . '.uid}'];
        }
        $tableDefinition = $tableDefinitionCollection->getTable($table);
        if ($table === 'tt_content') {
            return ['<f:cObject typoscriptObjectPath="tt_content" table="tt_content" data="{' . $identifier . '}"/>'];
        }
        $contentType = $tableDefinition->getDefaultTypeDefinition();
        $result = $this->createFieldVariables($contentType, $tableDefinitionCollection, false, ++$depth, $identifier);
        return $result;
    }

    /**
     * @return array<string>
     */
    protected function createPagePreview(): array
    {
        $defaultContent = [];
        $defaultContent[] = '<html xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers" xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers" data-namespace-typo3-fluid="true">';
        $defaultContent[] = '    <div class="card card-size-medium">';
        $defaultContent[] = '        <div class="card-body">';
        $defaultContent[] = '            <dl class="row">';
        $defaultContent[] = '                <dt class="col-sm-3">Title:</dt>';
        $defaultContent[] = '                <dd class="col-sm-9">';
        $defaultContent[] = '                   {data.title}';
        $defaultContent[] = '                </dd>';
        $defaultContent[] = '            </dl>';
        $defaultContent[] = '            <be:link.editRecord class="btn btn-default" uid="{data.uid}" table="{data.mainType}" fields="title">';
        $defaultContent[] = '               Edit page properties';
        $defaultContent[] = '            </be:link.editRecord>';
        $defaultContent[] = '        </div>';
        $defaultContent[] = '    </div>';
        $defaultContent[] = '</html>';
        return $defaultContent;
    }

    protected function createElementPreview(LoadedContentBlock $contentBlock, TableDefinitionCollection $tableDefinitionCollection): array
    {
        $typeName = $contentBlock->getYaml()['typeName'];
        $contentElementDefinition = $tableDefinitionCollection->getContentElementDefinition($typeName);
        $defaultContent = [];
        $defaultContent[] = '<html';
        $defaultContent[] = '    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"';
        $defaultContent[] = '    xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"';
        $defaultContent[] = '    data-namespace-typo3-fluid="true"';
        $defaultContent[] = '>';
        $defaultContent[] = '';
        $defaultContent[] = '<f:layout name="Preview"/>';
        $defaultContent[] = '';
        $defaultContent[] = '<f:section name="Header">';
        $defaultContent[] = '    <cb:link.editRecord uid="{data.uid}" table="{data.mainType}">{data.header}</cb:link.editRecord>';
        $defaultContent[] = '</f:section>';
        $defaultContent[] = '';
        $defaultContent[] = '<f:section name="Content">';
        $defaultContent = array_merge(
            $defaultContent,
            array_map(
                fn(string $line): string => '    ' . $line,
                $this->createFieldVariables($contentElementDefinition, $tableDefinitionCollection, true),
            ),
        );
        $defaultContent[] = '</f:section>';
        $defaultContent[] = '';
        $defaultContent[] = '<f:comment>';
        $defaultContent[] = '<!-- Uncomment to override preview footer -->';
        $defaultContent[] = '<f:section name="Footer">';
        $defaultContent[] = '    My custom Footer';
        $defaultContent[] = '</f:section>';
        $defaultContent[] = '</f:comment>';
        $defaultContent[] = '</html>';
        return $defaultContent;
    }
}
