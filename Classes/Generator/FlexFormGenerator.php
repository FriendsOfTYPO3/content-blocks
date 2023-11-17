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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal Not part of TYPO3's public API.
 */
class FlexFormGenerator
{
    public function generate(FlexFormDefinition $flexFormDefinition): string
    {
        $sheets = [];
        foreach ($flexFormDefinition as $sheetDefinition) {
            $sheet = $this->processSheet($sheetDefinition);
            $root = [
                'type' => 'array',
                'el' => $sheet,
            ];
            if (!$flexFormDefinition->hasDefaultSheet()) {
                $root['sheetTitle'] = $sheetDefinition->getLabel();
                $root['sheetDescription'] = $sheetDefinition->getDescription();
                $root['sheetShortDescr'] = $sheetDefinition->getLinkTitle();
            }
            $sheets[$sheetDefinition->getKey()] = [
                'ROOT' => $root,
            ];
        }
        $dataStructure['sheets'] = $sheets;
        $flexForm = GeneralUtility::array2xml($dataStructure, '', 0, 'T3FlexForms', 4);
        return $flexForm;
    }

    protected function processSheet(SheetDefinition $sheetDefinition): array
    {
        $fields = [];
        foreach ($sheetDefinition as $tcaFieldOrSection) {
            $field = match ($tcaFieldOrSection::class) {
                SectionDefinition::class => $this->processSection($tcaFieldOrSection),
                TcaFieldDefinition::class => $this->processTcaField($tcaFieldOrSection),
            };
            $fields[$tcaFieldOrSection->getIdentifier()] = $field;
        }
        return $fields;
    }

    protected function processSection(SectionDefinition $sectionDefinition): array
    {
        $result = [
            'title' => $sectionDefinition->getLanguagePath(),
            'type' => 'array',
            'section' => 1,
        ];
        $processedContainers = [];
        foreach ($sectionDefinition as $container) {
            $containerResult = [
                'title' => $container->getLanguagePath(),
                'type' => 'array',
            ];
            $processedContainerFields = [];
            foreach ($container as $containerField) {
                $processedContainerFields[$containerField->getIdentifier()] = $this->processTcaField($containerField);
            }
            $containerResult['el'] = $processedContainerFields;
            $processedContainers[$container->getIdentifier()] = $containerResult;
        }
        $result['el'] = $processedContainers;
        return $result;
    }

    protected function processTcaField(TcaFieldDefinition $flexFormTcaDefinition): array
    {
        $flexFormTca = $flexFormTcaDefinition->getTca();
        $languagePath = $flexFormTcaDefinition->getLanguagePath();
        // FlexForm child fields can't be excluded.
        unset($flexFormTca['exclude']);
        $flexFormTca['label'] = $languagePath->getCurrentPath() . '.label';
        $flexFormTca['description'] = $languagePath->getCurrentPath() . '.description';
        return $flexFormTca;
    }
}
