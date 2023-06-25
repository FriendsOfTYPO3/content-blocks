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

namespace TYPO3\CMS\ContentBlocks\Definition;

/**
 * @internal Not part of TYPO3's public API.
 */
final class PaletteDefinition
{
    private string $identifier = '';
    private string $label = '';
    private string $description = '';
    /** @var string[] */
    public array $showitem = [];

    public function __construct(string $identifier, string $label, string $description, array $showitem)
    {
        if ($identifier === '') {
            throw new \InvalidArgumentException('Palette identifier must not be empty.', 1629293639);
        }

        $this->identifier = $identifier;
        $this->label = $label;
        $this->description = $description;
        $this->showitem = $showitem;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTca(): array
    {
        return [
            'label' => $this->label,
            'description' => $this->description,
            'showitem' => implode(',', $this->showitem),
        ];
    }
}
