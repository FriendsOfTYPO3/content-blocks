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

namespace TYPO3\CMS\ContentBlocks\Schema;

use TYPO3\CMS\ContentBlocks\FieldType\FieldTypeInterface;
use TYPO3\CMS\ContentBlocks\FieldType\TcaFieldTypeRegistry;

/**
 * @internal Not part of TYPO3's public API.
 */
class FieldTypeResolver
{
    public function __construct(
        protected TcaFieldTypeRegistry $fieldTypeRegistry,
    ) {}

    public function resolve(array $configuration): FieldTypeInterface
    {
        if ($configuration === [] || !isset($configuration['config']['type'])) {
            throw new \InvalidArgumentException('Tried to resolve type of non-existing field.', 1680110446);
        }
        $tcaType = $configuration['config']['type'];
        if (!$this->fieldTypeRegistry->has($tcaType)) {
            throw new \InvalidArgumentException(
                'Field type "' . $tcaType . '" is either not implemented or cannot be shared in Content Blocks.',
                1680110918
            );
        }
        $fieldType = $this->fieldTypeRegistry->get($tcaType);
        return $fieldType;
    }
}
