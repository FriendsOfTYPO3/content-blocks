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

namespace TYPO3\CMS\ContentBlocks\FieldType;

abstract class AbstractFieldType implements FieldTypeInterface
{
    protected string $name;
    protected string $tcaType;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTcaType(string $tcaType): void
    {
        $this->tcaType = $tcaType;
    }

    public function getTcaType(): string
    {
        return $this->tcaType;
    }

    public function getSql(string $column): string
    {
        return '';
    }
}
