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

namespace TYPO3\CMS\ContentBlocks\ViewHelpers\Link;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Domain\RecordInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\InvalidArgumentValueException;

/**
 * Use this ViewHelper to provide edit links to records. The ViewHelper will
 * pass the uid and table to FormEngine.
 *
 * The uid must be given as a positive integer.
 * For new records, use the :ref:`<be:link.newRecordViewHelper> <typo3-backend-link-newrecord>`.
 *
 * Examples
 * ========
 *
 * Link to the record-edit action passed to FormEngine::
 *
 *    <cb:link.editRecord uid="42" table="a_table" returnUrl="foo/bar" />
 *
 * Output::
 *
 *    <a href="/typo3/record/edit?edit[a_table][42]=edit&returnUrl=foo/bar">
 *        Edit record
 *    </a>
 *
 * Link to edit page uid=3 and then return back to the BE module "web_MyextensionList"::
 *
 *    <cb:link.editRecord uid="3" table="pages" returnUrl="{f:be.uri(route: 'web_MyextensionList')}">
 *
 * Link to edit only the fields title and subtitle of page uid=42 and return to foo/bar::
 *
 *    <cb:link.editRecord uid="42" table="pages" fields="title,subtitle" returnUrl="foo/bar">
 *        Edit record
 *    </cb:link.editRecord>
 *
 * Output::
 *
 *    <a href="/typo3/record/edit?edit[pages][42]=edit&returnUrl=foo/bar&columnsOnly[pages]=title,subtitle">
 *        Edit record
 *    </a>
 *
 * Open the record in the contextual editing overlay::
 *
 *    <cb:link.editRecord uid="42" table="pages" fields="title,subtitle" contextual="true">
 *        Edit page properties
 *    </cb:link.editRecord>
 *
 * Output::
 *
 *    <typo3-backend-contextual-record-edit-trigger
 *        url="/typo3/record/edit/contextual?edit[pages][42]=edit&columnsOnly[pages][0]=title&columnsOnly[pages][1]=subtitle"
 *        edit-url="/typo3/record/edit?edit[pages][42]=edit&columnsOnly[pages][0]=title&columnsOnly[pages][1]=subtitle"
 *    >
 *        Edit page properties
 *    </typo3-backend-contextual-record-edit-trigger>
 *
 * @see https://docs.typo3.org/permalink/t3viewhelper:typo3-backend-link-editrecord
 */
final class EditRecordViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    public function __construct(
        private readonly UriBuilder $uriBuilder,
        private readonly PageRenderer $pageRenderer,
    ) {
        parent::__construct();
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('uid', 'int', 'uid of record to be edited');
        $this->registerArgument('table', 'string', 'target database table');
        $this->registerArgument('fields', 'string', 'Edit only these fields (comma separated list)');
        $this->registerArgument('module', 'string', 'Set module identifier for context - marking as active when editing the record', false, '');
        $this->registerArgument('returnUrl', 'string', 'return to this URL after closing the edit dialog', false, '');
        $this->registerArgument('record', 'object', 'The Record Object can be used instead of uid and table', false, '');
        $this->registerArgument('contextual', 'bool', 'Render a contextual edit trigger instead of a classic edit link', false, false);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws RouteNotFoundException
     */
    public function render(): string
    {
        if (($this->arguments['record'] ?? null) instanceof RecordInterface) {
            $this->arguments['uid'] = $this->arguments['record']->getUid();
            $this->arguments['table'] = $this->arguments['record']->getMainType();
        }
        if ($this->arguments['uid'] < 1) {
            throw new InvalidArgumentValueException('Uid must be a positive integer, ' . $this->arguments['uid'] . ' given.', 1526127158);
        }
        $request = $this->renderingContext->hasAttribute(ServerRequestInterface::class)
            ? $this->renderingContext->getAttribute(ServerRequestInterface::class) : null;

        if (empty($this->arguments['returnUrl']) && $request !== null) {
            // @todo: We may want to deprecate fetching returnUrl from request
            $this->arguments['returnUrl'] = $request->getAttribute('normalizedParams')->getRequestUri();
        }
        if ($this->arguments['contextual'] ?? false) {
            $params = $this->buildParams($this->arguments['returnUrl'], $request);
            $this->pageRenderer->loadJavaScriptModule('@typo3/backend/element/contextual-record-edit-trigger.js');
            $this->tag->setTagName('typo3-backend-contextual-record-edit-trigger');
            $this->tag->addAttribute('url', (string)$this->uriBuilder->buildUriFromRoute('record_edit_contextual', $params));
            $this->tag->addAttribute('edit-url', (string)$this->uriBuilder->buildUriFromRoute('record_edit', $params));
            $this->tag->addAttribute('context', $this->arguments['table']);
        } else {
            $uri = $this->buildUri($request);
            $this->tag->addAttribute('href', $uri);
        }

        $this->tag->setContent((string)$this->renderChildren());
        $this->tag->forceClosingTag(true);
        return $this->tag->render();
    }

    /**
     * @throws RouteNotFoundException
     */
    protected function buildUri(?ServerRequestInterface $request): string
    {
        $fragment = '#element-' . $this->arguments['table'] . '-' . $this->arguments['uid'];
        $returnUrl = $this->arguments['returnUrl'] . $fragment;
        $params = $this->buildParams($returnUrl, $request);
        $uri = (string)$this->uriBuilder->buildUriFromRoute('record_edit', $params);
        return $uri;
    }

    protected function buildParams(string $returnUrl, ?ServerRequestInterface $request): array
    {
        $params = [
            'edit' => [$this->arguments['table'] => [$this->arguments['uid'] => 'edit']],
            'module' => ($this->arguments['module'] ?? '') ?: ($request?->getAttribute('module')?->getIdentifier() ?? ''),
            'returnUrl' => $returnUrl,
        ];
        if ($this->arguments['fields'] ?? false) {
            $params['columnsOnly'] = [
                $this->arguments['table'] => GeneralUtility::trimExplode(',', $this->arguments['fields'], true),
            ];
        }
        return $params;
    }
}
