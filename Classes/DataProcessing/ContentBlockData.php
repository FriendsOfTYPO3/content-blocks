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

namespace TYPO3\CMS\ContentBlocks\DataProcessing;

/**
 * This class represents the `data` object inside the Fluid template for Content Blocks.
 *
 * Usage in Fluid
 * ==============
 *
 * To access own custom fields, use: `data.identifier`.
 * E.g.:
 * - {data.header}
 * - {data.collection_field.text_field}
 *
 * There are some special accessors, which are always available:
 * - {data.uid}
 * - {data.pid}
 * - {data.typeName}
 *
 * These special accessors are available, if the corresponding features are turned on
 * (Always true for Content Elements):
 *  - {data.languageId} (YAML: languageAware: true)
 *  - {data.creationDate} (YAML: trackCreationDate: true)
 *  - {data.updateDate} (YAML: trackUpdateDate: true)
 *
 * These special accessors are available depending on the context:
 * - {data.localizedUid}
 * - {data.originalUid}
 * - {data.originalPid}
 *
 * To access the raw database record use:
 * - {data._raw.some_field}
 *
 * @internal This is not public TYPO3 PHP API. Only to be used inside of Fluid templates by accessing as variable.
 */
final class ContentBlockData extends \stdClass
{
    /**
     * This is a hint for f:debug users.
     */
    public string $_debug_hint = 'To access data under `_processed` you must omit the key: {data.identifier}';

    public function __construct(
        private readonly string $_name = '',
        private readonly array $_raw = [],
        /** @var array<string, RelationGrid> */
        private readonly array $_grids = [],
        private readonly array $_processed = [],
    ) {}

    public function __get(string $name = ''): mixed
    {
        if ($name === '_name') {
            return $this->_name;
        }
        if ($name === '_raw') {
            return $this->_raw;
        }
        if ($name === '_grids') {
            return $this->_grids;
        }
        if (array_key_exists($name, $this->_processed)) {
            return $this->_processed[$name];
        }
        return null;
    }
}
