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

namespace TYPO3\CMS\ContentBlocks\CodeGenerator;

use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;

class TcaCodeGenerator
{
    public function generateTca(array $yamlConfiguration): array
    {
        $result = [];
        foreach ($yamlConfiguration['fields'] as $field) {
            $fieldType = FieldType::from($field['type']);
            $config = $fieldType->getTca();
            foreach ($field['properties'] as $key => $value) {
                if ($key === 'trim' && $value === '1') {
                    $config['eval'] = 'trim';
                    continue;
                }
                $config[$key] = $value;
            }
            $result[]['config'] = $config;
        }
        return $result;
    }
}
