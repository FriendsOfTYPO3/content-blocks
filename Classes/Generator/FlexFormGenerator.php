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

use TYPO3\CMS\ContentBlocks\Definition\FlexForm\FlexFormDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SectionDefinition;
use TYPO3\CMS\ContentBlocks\Definition\FlexForm\SheetDefinition;
use TYPO3\CMS\ContentBlocks\Definition\TcaFieldDefinition;
use TYPO3\CMS\ContentBlocks\Registry\LanguageFileRegistryInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class FlexFormGenerator
{
    public function __construct(protected readonly LanguageFileRegistryInterface $languageFileRegistry) {}

    public function generate(FlexFormDefinition $flexFormDefinition): string
    {
        $sheets = [];
        foreach ($flexFormDefinition as $sheetDefinition) {
            $sheet = $this->processSheet($sheetDefinition, $flexFormDefinition);
            $root = [
                'type' => 'array',
                'el' => $sheet,
            ];
            if (!$flexFormDefinition->hasDefaultSheet()) {
                $root = $this->resolveLabels($flexFormDefinition, $sheetDefinition, $root);
            }
            $sheets[$sheetDefinition->getIdentifier()] = [
                'ROOT' => $root,
            ];
        }
        $dataStructure['sheets'] = $sheets;
        $flexForm = GeneralUtility::array2xml($dataStructure, '', 0, 'T3FlexForms', 4);
        return $flexForm;
    }

    protected function processSheet(SheetDefinition $sheetDefinition, FlexFormDefinition $flexFormDefinition): array
    {
        $fields = [];
        foreach ($sheetDefinition as $tcaFieldOrSection) {
            $field = match ($tcaFieldOrSection::class) {
                SectionDefinition::class => $this->processSection($tcaFieldOrSection, $flexFormDefinition),
                TcaFieldDefinition::class => $this->processTcaField($tcaFieldOrSection, $flexFormDefinition),
            };
            $fields[$tcaFieldOrSection->getIdentifier()] = $field;
        }
        return $fields;
    }

    protected function processSection(SectionDefinition $sectionDefinition, FlexFormDefinition $flexFormDefinition): array
    {
        $result = [
            'title' => $sectionDefinition->getLabelPath(),
            'type' => 'array',
            'section' => 1,
        ];
        $processedContainers = [];
        foreach ($sectionDefinition as $container) {
            $containerResult = [
                'title' => $container->getLabelPath(),
                'type' => 'array',
            ];
            $processedContainerFields = [];
            foreach ($container as $containerField) {
                $processedContainerFields[$containerField->getIdentifier()] = $this->processTcaField($containerField, $flexFormDefinition);
            }
            $containerResult['el'] = $processedContainerFields;
            $processedContainers[$container->getIdentifier()] = $containerResult;
        }
        $result['el'] = $processedContainers;
        return $result;
    }

    protected function processTcaField(TcaFieldDefinition $flexFormTcaDefinition, FlexFormDefinition $flexFormDefinition): array
    {
        $flexFormTca = $flexFormTcaDefinition->getTca();
        // FlexForm child fields can't be excluded.
        unset($flexFormTca['exclude']);

        $tcaLabel = $flexFormTcaDefinition->getTca()['label'] ?? '';
        $labelPath = $flexFormTcaDefinition->getLabelPath();
        if ($tcaLabel === '') {
            if ($this->languageFileRegistry->isset($flexFormDefinition->getContentBlockName(), $labelPath)) {
                $flexFormTca['label'] = $labelPath;
            } else {
                $flexFormTca['label'] = $flexFormTcaDefinition->getIdentifier();
            }
        } else {
            $flexFormTca['label'] = $tcaLabel;
        }

        $tcaDescription = $flexFormTcaDefinition->getTca()['description'] ?? '';
        $descriptionPath = $flexFormTcaDefinition->getDescriptionPath();
        if ($tcaDescription === '') {
            if ($this->languageFileRegistry->isset($flexFormDefinition->getContentBlockName(), $descriptionPath)) {
                $flexFormTca['description'] = $descriptionPath;
            }
        } else {
            $flexFormTca['description'] = $tcaDescription;
        }
        return $flexFormTca;
    }

    protected function resolveLabels(FlexFormDefinition $flexFormDefinition, SheetDefinition $sheetDefinition, array $root): array
    {
        if ($this->languageFileRegistry->isset($flexFormDefinition->getContentBlockName(), $sheetDefinition->getLanguagePathLabel())) {
            $root['sheetTitle'] = $sheetDefinition->getLanguagePathLabel();
        } else {
            if ($sheetDefinition->hasLabel()) {
                $root['sheetTitle'] = $sheetDefinition->getLabel();
            } else {
                $root['sheetTitle'] = $sheetDefinition->getIdentifier();
            }
        }
        if ($this->languageFileRegistry->isset($flexFormDefinition->getContentBlockName(), $sheetDefinition->getLanguagePathDescription())) {
            $root['sheetDescription'] = $sheetDefinition->getLanguagePathDescription();
        } elseif ($sheetDefinition->hasDescription()) {
            $root['sheetDescription'] = $sheetDefinition->getDescription();
        }
        if ($this->languageFileRegistry->isset($flexFormDefinition->getContentBlockName(), $sheetDefinition->getLanguagePathLinkTitle())) {
            $root['sheetShortDescr'] = $sheetDefinition->getLanguagePathLinkTitle();
        } elseif ($sheetDefinition->hasLinkTitle()) {
            $root['sheetShortDescr'] = $sheetDefinition->getLinkTitle();
        }
        return $root;
    }
}
