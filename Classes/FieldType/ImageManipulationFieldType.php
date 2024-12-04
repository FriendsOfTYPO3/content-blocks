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

/**
 * @internal Not part of TYPO3's public API.
 */
#[FieldType(name: 'ImageManipulation', tcaType: 'imageManipulation')]
final class ImageManipulationFieldType extends AbstractFieldType
{
    use WithCommonProperties;

    public function createFromArray(array $settings): ImageManipulationFieldType
    {
        $self = clone $this;
        $self->setCommonProperties($settings);
        return $self;
    }

    public function getTca(): array
    {
        $tca = $this->toTca();
        $config['type'] = $this->getTcaType();
        $tca['config'] = array_replace($tca['config'] ?? [], $config);
        return $tca;
    }
}
