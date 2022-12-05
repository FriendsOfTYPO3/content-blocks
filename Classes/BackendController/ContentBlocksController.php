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

namespace TYPO3\CMS\ContentBlocks\BackendController;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

/**
 * @internal
 */
class ContentBlocksController
{
    const ROUTE_IDENTIFIER = 'tools_contentblocks';

    public function __construct(
        protected readonly IconFactory $iconFactory,
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected readonly PageRenderer $pageRenderer,
        protected readonly UriBuilder $uriBuilder,
    ) {
    }

    public function overviewAction(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $this->configureDocHeader($view, $request->getAttribute('normalizedParams')->getRequestUri());
        $view->setTitle(
            $this->getLanguageService()->sL(
                'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf:mlang_tabs_tab'
            )
        );

        $this->addJavascriptGlobals();

        return $view->renderResponse('Overview');
    }

    public function newAction(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $this->configureDocHeader($view, $request->getAttribute('normalizedParams')->getRequestUri());
        $view->setTitle(
            $this->getLanguageService()->sL(
                'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf:mlang_tabs_tab'
            )
        );

        $this->addJavascriptGlobals();

        return $view->renderResponse('New');
    }

    public function editAction(ServerRequestInterface $request): ResponseInterface
    {
        $view = $this->moduleTemplateFactory->create($request);
        $this->configureDocHeader($view, $request->getAttribute('normalizedParams')->getRequestUri());
        $view->setTitle(
            $this->getLanguageService()->sL(
                'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf:mlang_tabs_tab'
            )
        );

        $this->addJavascriptGlobals();

        $cType = $request->getQueryParams()['cType'] ?? null;
        if ($cType === null) {
            $view->assign('new', 1);
        } else {
            $view->assign('cType', $cType);
        }

        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();
        $closeButton = $buttonBar->makeLinkButton()
            ->setDataAttributes(['identifier' => 'closeButton'])
            ->setHref((string)$this->uriBuilder->buildUriFromRoute('web_FormFormbuilder'))
            ->setClasses('t3-form-element-close-form-button hidden')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.closeDoc'))
            ->setIcon($this->iconFactory->getIcon('actions-close', Icon::SIZE_SMALL));

        $saveButton = $buttonBar->makeInputButton()
            ->setDataAttributes(['identifier' => 'saveButton'])
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:formEditor.save_button'))
            ->setName('formeditor-save-form')
            ->setValue('save')
            ->setClasses('t3-form-element-save-form-button hidden')
            ->setIcon($this->iconFactory->getIcon('actions-document-save', Icon::SIZE_SMALL))
            ->setShowLabelText(true);

        $formSettingsButton = $buttonBar->makeInputButton()
            ->setDataAttributes(['identifier' => 'formSettingsButton'])
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:formEditor.form_settings_button'))
            ->setName('formeditor-form-settings')
            ->setValue('settings')
            ->setClasses('t3-form-element-form-settings-button hidden')
            ->setIcon($this->iconFactory->getIcon('actions-system-extension-configure', Icon::SIZE_SMALL))
            ->setShowLabelText(true);

        $undoButton = $buttonBar->makeInputButton()
            ->setDataAttributes(['identifier' => 'undoButton'])
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:formEditor.undo_button'))
            ->setName('formeditor-undo-form')
            ->setValue('undo')
            ->setClasses('t3-form-element-undo-form-button hidden disabled')
            ->setIcon($this->iconFactory->getIcon('actions-edit-undo', Icon::SIZE_SMALL));

        $redoButton = $buttonBar->makeInputButton()
            ->setDataAttributes(['identifier' => 'redoButton'])
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:form/Resources/Private/Language/Database.xlf:formEditor.redo_button'))
            ->setName('formeditor-redo-form')
            ->setValue('redo')
            ->setClasses('t3-form-element-redo-form-button hidden disabled')
            ->setIcon($this->iconFactory->getIcon('actions-edit-redo', Icon::SIZE_SMALL));

        $buttonBar->addButton($closeButton, ButtonBar::BUTTON_POSITION_LEFT, 2);
        $buttonBar->addButton($saveButton, ButtonBar::BUTTON_POSITION_LEFT, 3);
        $buttonBar->addButton($formSettingsButton, ButtonBar::BUTTON_POSITION_LEFT, 4);
        $buttonBar->addButton($undoButton, ButtonBar::BUTTON_POSITION_LEFT, 5);
        $buttonBar->addButton($redoButton, ButtonBar::BUTTON_POSITION_LEFT, 5);

        return $view->renderResponse('Edit');
    }

    protected function configureDocHeader(ModuleTemplate $view, string $requestUri): void
    {
        $buttonBar = $view->getDocHeaderComponent()->getButtonBar();

        $addButton = $buttonBar->makeLinkButton()
            ->setHref('#')
            ->setDataAttributes(['identifier' => 'contentblocks.action.create'])
            ->setTitle(
                $this->getLanguageService()->sL(
                    'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf:contentblocks.action.create'
                )
            )
            ->setIcon($this->iconFactory->getIcon('actions-add', Icon::SIZE_SMALL))
            ->setShowLabelText(true);
        $buttonBar->addButton($addButton);

        $reloadButton = $buttonBar->makeLinkButton()
            ->setHref($requestUri)
            ->setTitle(
                $this->getLanguageService()->sL(
                    'LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:labels.reload'
                )
            )
            ->setIcon($this->iconFactory->getIcon('actions-refresh', Icon::SIZE_SMALL));
        $buttonBar->addButton($reloadButton, ButtonBar::BUTTON_POSITION_RIGHT, 2);

        $shortcutButton = $buttonBar->makeShortcutButton()
            ->setRouteIdentifier(self::ROUTE_IDENTIFIER)
            ->setDisplayName(
                $this->getLanguageService()->sL(
                    'LLL:EXT:content_blocks/Resources/Private/Language/locallang_module.xlf:mlang_labels_tablabel'
                )
            );
        $buttonBar->addButton($shortcutButton, ButtonBar::BUTTON_POSITION_RIGHT, 2);
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function addJavascriptGlobals()
    {
        $this->pageRenderer->addInlineSetting(
            'urls',
            'contentblocks/contentBlock/edit',
            (string)$this->uriBuilder->buildUriFromRoute(
                ContentBlocksController::ROUTE_IDENTIFIER . '/contentBlock/edit'
            )
        );

        // We need this in the global (MultiStepWizard) scope but do not want to load it for every TYPO3 module.
        // Thus we are including the module in the MultiStepWizard slide via <script>.
        $this->pageRenderer->addInlineSetting(
            'urls',
            'EXT:content_blocks/Resources/Public/JavaScript/element/choose-name-element.js',
            GeneralUtility::createVersionNumberedFilename(
                PathUtility::getAbsoluteWebPath(
                    GeneralUtility::getFileAbsFileName(
                        'EXT:content_blocks/Resources/Public/JavaScript/element/content_blocks-choose-name-element.js'
                    )
                )
            )
        );

        $this->pageRenderer->addInlineLanguageLabelFile(
            'EXT:content_blocks/Resources/Private/Language/locallang_module.xlf',
            '',
        );
        $this->pageRenderer->addInlineLanguageLabelFile(
            'EXT:content_blocks/Resources/Private/Language/locallang_definition.xlf',
            '',
        );
    }
}
