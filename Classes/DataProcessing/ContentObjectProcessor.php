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

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * @internal Not part of TYPO3's public API.
 */
class ContentObjectProcessor
{
    private ServerRequestInterface $request;

    public function __construct(
        protected readonly ContentObjectRenderer $contentObjectRenderer,
        protected readonly ContentObjectProcessorSession $session,
    ) {}

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
        $this->contentObjectRenderer->setRequest($request);
    }

    public function processContentObject(ContentBlockData $contentBlockData): RenderedGridItem
    {
        if ($this->session->hasRenderedGrid($contentBlockData)) {
            return $this->session->getRenderedGrid($contentBlockData);
        }
        $renderedGridItem = new RenderedGridItem();
        $this->session->addRenderedGrid($contentBlockData, new RenderedGridItem());
        $frontendTypoScript = $this->request->getAttribute('frontend.typoscript');
        $setup = $frontendTypoScript->getSetupArray();
        $table = $contentBlockData->getMainType();
        $this->contentObjectRenderer->start($contentBlockData->toArray(), $table);
        $typeName = $contentBlockData->getRecordType();
        $typoScriptObjectPath = $table . '.' . $typeName;
        $pathSegments = GeneralUtility::trimExplode('.', $typoScriptObjectPath);
        $lastSegment = (string)array_pop($pathSegments);
        foreach ($pathSegments as $segment) {
            if (!array_key_exists($segment . '.', $setup)) {
                return $renderedGridItem;
            }
            $setup = $setup[$segment . '.'];
        }
        if (!isset($setup[$lastSegment])) {
            return $renderedGridItem;
        }
        $name = $setup[$lastSegment];
        $conf = $setup[$lastSegment . '.'] ?? [];
        $content = $this->contentObjectRenderer->cObjGetSingle($name, $conf, $typoScriptObjectPath);
        $renderedGridItem->content = $content;
        $renderedGridItem->data = $contentBlockData;
        $this->session->setRenderedGrid($contentBlockData, $renderedGridItem);
        return $renderedGridItem;
    }
}
