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

namespace TYPO3\CMS\ContentBlocks\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\ContentBlocks\Loader\AssetPublisher;
use TYPO3\CMS\ContentBlocks\Registry\ContentBlockRegistry;
use TYPO3\CMS\Core\Attribute\AsNonSchedulableCommand;

#[AsCommand(
    'content-blocks:assets:publish',
    'Publish Content Blocks assets into Resources/Public folder of the host extension.',
)]
#[AsNonSchedulableCommand]
class PublishAssetsCommand extends Command
{
    public function __construct(
        protected readonly ContentBlockRegistry $contentBlockRegistry,
        protected readonly AssetPublisher $assetPublisher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $contentBlocks = $this->contentBlockRegistry->getAll();
        $this->assetPublisher->publishAssets($contentBlocks);
        return Command::SUCCESS;
    }
}
