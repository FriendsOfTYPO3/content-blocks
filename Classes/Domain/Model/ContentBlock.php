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

namespace TYPO3\CMS\ContentBlocks\Domain\Model;

/**
 * Class ContentBlock
 * "Transfers" the content data out from the database for the frontend.
 */
class ContentBlock
{
    protected int $uid = 0;

    public function __construct(int $uid)
    {
        $this->createFromUid($uid);
    }

    protected function createFromUid(int $uid)
    {
        $this->uid = $uid;
    }

    /** Function getDataForDataProcessor
     * TODO: Return the data for the DataProcessor
     */
    public function getDataForDataProcessor(): array
    {
        // TODO: fill in functionality
        return [];
    }
}
