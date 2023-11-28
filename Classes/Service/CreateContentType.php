<?php

namespace TYPO3\CMS\ContentBlocks\Service;

class CreateContentType
{
    public function createContentBlockContentElementConfiguration(
        string $vendor,
        string $name,
        array $fields,
        array $basics = [],
        $group = 'common',
        bool $prefixFields = true,
        string $prefixType = 'full',
        string $table = 'tt_content',
        string $typeField = 'CType',
        ?string $type = ''
    ): array
    {
        $configuration = [
            'name' => $vendor . '/' . $name,
            'group' => $group,
            'prefixFields' => $prefixFields,
            'prefixType' => $prefixType,
            'table' => $table,
            'typeField' => $typeField,
            'basics' => $basics,
            'fields' => $fields
        ];
        if ($type !== '' && $type !== null) {
            $configuration['typeName'] = $type;
        }
        return $configuration;
    }

    public function createContentBlockPageTypeConfiguration(string $vendor, string $name, int $type): array
    {
        return [
            'name' => $vendor . '/' . $name,
            'typeName' => $type,
            'prefixFields' => true,
            'prefixType' => 'full',
        ];
    }

    public function createContentBlockRecordTypeConfiguration(string $vendor, string $name, ?string $type = ''): array
    {
        $vendorWithoutSeparator = str_replace('-', '', $vendor);
        $nameWithoutSeparator = str_replace('-', '', $name);
        $configuration = [
            'name' => $vendor . '/' . $name,
            'table' => 'tx_' . $vendorWithoutSeparator . '_domain_model_' . $nameWithoutSeparator,
            'prefixFields' => false,
            'labelField' => 'title',
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
