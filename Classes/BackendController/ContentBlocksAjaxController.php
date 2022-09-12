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
use TYPO3\CMS\ContentBlocks\Domain\Model\ContentBlockConfiguration;
use TYPO3\CMS\ContentBlocks\Domain\Repository\ContentBlockConfigurationRepository;
use TYPO3\CMS\ContentBlocks\Enumeration\FieldType;
use TYPO3\CMS\ContentBlocks\FieldConfiguration\AbstractFieldConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * @internal
 */
class ContentBlocksAjaxController
{
    const ROUTE_IDENTIFIER = 'tools_contentblocks/ajax';

    public function __construct(
        protected readonly ContentBlockConfigurationRepository $contentBlockConfigurationRepository
    ) {
    }

    public function jsonContentBlockGetAction(ServerRequestInterface $request): ResponseInterface
    {
        // @todo

        return new JsonResponse(
            $this->cbConfigFixture()['typo3-contentblocks_fluid-styled-content-example-local']
        );
    }


    public function jsonContentBlocksListAction(ServerRequestInterface $request): ResponseInterface
    {
        $cbs = $this->contentBlockConfigurationRepository->findAll();

        $this->enrichContentblocksForBackend($cbs);

        return new JsonResponse($cbs);

//        return new JsonResponse(
//            $this->cbConfigFixture()
//        );
    }

    public function jsonCreateAction(ServerRequestInterface $request): ResponseInterface
    {
        return new JsonResponse(
            [
                'success' => true,
                'message' => 'Content Block created // DB fields have been created // clear caches now',
            ]
        );
    }

    private function cbConfigFixture()
    {
        return [
            'typo3-contentblocks_fluid-styled-content-example-local' =>
                [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'fluid-styled-content-example-local',
                    'key' => 'fluid-styled-content-example-local',
                    'path' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/',
                    'srcPath' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src/',
                    'distPath' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/dist/',
                    'icon' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/ContentBlockIcon.svg',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider',
                    'CType' => 'typo3-contentblocks_fluid-styled-content-example-local',
                    'fields' =>
                        [
                            'amount' =>
                                [
                                    'identifier' => 'amount',
                                    'type' => 'Number',
                                    'properties' =>
                                        [
                                            'defaultValue' => 0,
                                            'maximum' => 100,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'amount',
                                        ],
                                    '_identifier' => 'amount',
                                ],
                            'icon' =>
                                [
                                    'identifier' => 'icon',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'allowedExtensions' => 'svg',
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'icon',
                                        ],
                                    '_identifier' => 'icon',
                                ],
                            'text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                        ],
                    'collectionFields' =>
                        [
                        ],
                    'fileFields' =>
                        [
                            'icon' =>
                                [
                                    'identifier' => 'icon',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'allowedExtensions' => 'svg',
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'icon',
                                        ],
                                    '_identifier' => 'icon',
                                ],
                        ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src/Layouts',
                    'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/fluid-styled-content-example-local/src/EditorPreview.html',
                    'EditorInterfaceXlf' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src/Language/EditorInterface.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/fluid-styled-content-example-local/src/Language/EditorInterface.xlf:typo3-contentblocks.fluid-styled-content-example-local',
                    'FrontendXlf' => 'typo3conf/contentBlocks/fluid-styled-content-example-local/src/Language/Frontend.xlf',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/fluid-styled-content-example-local/src/Language/Frontend.xlf:typo3-contentblocks.fluid-styled-content-example-local',
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'amount',
                                            'type' => 'Number',
                                            'properties' =>
                                                [
                                                    'defaultValue' => 0,
                                                    'maximum' => 100,
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'amount',
                                                ],
                                            '_identifier' => 'amount',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'icon',
                                            'type' => 'Image',
                                            'properties' =>
                                                [
                                                    'allowedExtensions' => 'svg',
                                                    'minItems' => 1,
                                                    'maxItems' => 1,
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'icon',
                                                ],
                                            '_identifier' => 'icon',
                                        ],
                                    2 =>
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Text',
                                            '_path' =>
                                                [
                                                    0 => 'text',
                                                ],
                                            '_identifier' => 'text',
                                        ],
                                ],
                        ],
                ],
            'typo3-contentblocks_slider-local' =>
                [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'slider-local',
                    'key' => 'slider-local',
                    'path' => 'typo3conf/contentBlocks/slider-local/',
                    'srcPath' => 'typo3conf/contentBlocks/slider-local/src/',
                    'distPath' => 'typo3conf/contentBlocks/slider-local/dist/',
                    'icon' => 'typo3conf/contentBlocks/slider-local/ContentBlockIcon.png',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider',
                    'CType' => 'typo3-contentblocks_slider-local',
                    'fields' =>
                        [
                            'slides' =>
                                [
                                    'identifier' => 'slides',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'useAsLabel' => 'headline',
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'image',
                                                            'type' => 'Image',
                                                            'properties' =>
                                                                [
                                                                    'minItems' => 1,
                                                                    'maxItems' => 1,
                                                                    'required' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'image',
                                                                ],
                                                            '_identifier' => 'slides.image',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'headline',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'headline',
                                                                ],
                                                            '_identifier' => 'slides.headline',
                                                        ],
                                                    2 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'TextMultiline',
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'text',
                                                                ],
                                                            '_identifier' => 'slides.text',
                                                        ],
                                                    3 =>
                                                        [
                                                            'identifier' => 'buttonCaption',
                                                            'type' => 'Text',
                                                            'properties' =>
                                                                [
                                                                    'required' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'buttonCaption',
                                                                ],
                                                            '_identifier' => 'slides.buttonCaption',
                                                        ],
                                                    4 =>
                                                        [
                                                            'identifier' => 'buttonLink',
                                                            'type' => 'Url',
                                                            'properties' =>
                                                                [
                                                                    'linkTypes' =>
                                                                        [
                                                                            0 => 'page',
                                                                            1 => 'external',
                                                                        ],
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'buttonLink',
                                                                ],
                                                            '_identifier' => 'slides.buttonLink',
                                                        ],
                                                ],
                                            'maxItems' => 5,
                                            'minItems' => 1,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                        ],
                                    '_identifier' => 'slides',
                                ],
                            'slides.image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'image',
                                        ],
                                    '_identifier' => 'slides.image',
                                ],
                            'slides.headline' =>
                                [
                                    'identifier' => 'headline',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'headline',
                                        ],
                                    '_identifier' => 'slides.headline',
                                ],
                            'slides.text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'TextMultiline',
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'text',
                                        ],
                                    '_identifier' => 'slides.text',
                                ],
                            'slides.buttonCaption' =>
                                [
                                    'identifier' => 'buttonCaption',
                                    'type' => 'Text',
                                    'properties' =>
                                        [
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'buttonCaption',
                                        ],
                                    '_identifier' => 'slides.buttonCaption',
                                ],
                            'slides.buttonLink' =>
                                [
                                    'identifier' => 'buttonLink',
                                    'type' => 'Url',
                                    'properties' =>
                                        [
                                            'linkTypes' =>
                                                [
                                                    0 => 'page',
                                                    1 => 'external',
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'buttonLink',
                                        ],
                                    '_identifier' => 'slides.buttonLink',
                                ],
                            'autoplay' =>
                                [
                                    'identifier' => 'autoplay',
                                    'type' => 'Toggle',
                                    'properties' =>
                                        [
                                            'default' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'autoplay',
                                        ],
                                    '_identifier' => 'autoplay',
                                ],
                        ],
                    'collectionFields' =>
                        [
                            'slides' =>
                                [
                                    'identifier' => 'slides',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'useAsLabel' => 'headline',
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'image',
                                                            'type' => 'Image',
                                                            'properties' =>
                                                                [
                                                                    'minItems' => 1,
                                                                    'maxItems' => 1,
                                                                    'required' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'image',
                                                                ],
                                                            '_identifier' => 'slides.image',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'headline',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'headline',
                                                                ],
                                                            '_identifier' => 'slides.headline',
                                                        ],
                                                    2 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'TextMultiline',
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'text',
                                                                ],
                                                            '_identifier' => 'slides.text',
                                                        ],
                                                    3 =>
                                                        [
                                                            'identifier' => 'buttonCaption',
                                                            'type' => 'Text',
                                                            'properties' =>
                                                                [
                                                                    'required' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'buttonCaption',
                                                                ],
                                                            '_identifier' => 'slides.buttonCaption',
                                                        ],
                                                    4 =>
                                                        [
                                                            'identifier' => 'buttonLink',
                                                            'type' => 'Url',
                                                            'properties' =>
                                                                [
                                                                    'linkTypes' =>
                                                                        [
                                                                            0 => 'page',
                                                                            1 => 'external',
                                                                        ],
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'slides',
                                                                    1 => 'buttonLink',
                                                                ],
                                                            '_identifier' => 'slides.buttonLink',
                                                        ],
                                                ],
                                            'maxItems' => 5,
                                            'minItems' => 1,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                        ],
                                    '_identifier' => 'slides',
                                ],
                        ],
                    'fileFields' =>
                        [
                            'slides.image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'slides',
                                            1 => 'image',
                                        ],
                                    '_identifier' => 'slides.image',
                                ],
                        ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/slider-local/src',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/slider-local/src/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/slider-local/src/Layouts',
                    'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/slider-local/src/EditorPreview.html',
                    'EditorInterfaceXlf' => 'typo3conf/contentBlocks/slider-local/src/Language/EditorInterface.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/slider-local/src/Language/EditorInterface.xlf:typo3-contentblocks.slider-local',
                    'FrontendXlf' => 'typo3conf/contentBlocks/slider-local/src/Language/Frontend.xlf',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/slider-local/src/Language/Frontend.xlf:typo3-contentblocks.slider-local',
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'slides',
                                            'type' => 'Collection',
                                            'properties' =>
                                                [
                                                    'useAsLabel' => 'headline',
                                                    'fields' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'identifier' => 'image',
                                                                    'type' => 'Image',
                                                                    'properties' =>
                                                                        [
                                                                            'minItems' => 1,
                                                                            'maxItems' => 1,
                                                                            'required' => true,
                                                                        ],
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'slides',
                                                                            1 => 'image',
                                                                        ],
                                                                    '_identifier' => 'slides.image',
                                                                ],
                                                            1 =>
                                                                [
                                                                    'identifier' => 'headline',
                                                                    'type' => 'Text',
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'slides',
                                                                            1 => 'headline',
                                                                        ],
                                                                    '_identifier' => 'slides.headline',
                                                                ],
                                                            2 =>
                                                                [
                                                                    'identifier' => 'text',
                                                                    'type' => 'TextMultiline',
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'slides',
                                                                            1 => 'text',
                                                                        ],
                                                                    '_identifier' => 'slides.text',
                                                                ],
                                                            3 =>
                                                                [
                                                                    'identifier' => 'buttonCaption',
                                                                    'type' => 'Text',
                                                                    'properties' =>
                                                                        [
                                                                            'required' => true,
                                                                        ],
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'slides',
                                                                            1 => 'buttonCaption',
                                                                        ],
                                                                    '_identifier' => 'slides.buttonCaption',
                                                                ],
                                                            4 =>
                                                                [
                                                                    'identifier' => 'buttonLink',
                                                                    'type' => 'Url',
                                                                    'properties' =>
                                                                        [
                                                                            'linkTypes' =>
                                                                                [
                                                                                    0 => 'page',
                                                                                    1 => 'external',
                                                                                ],
                                                                        ],
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'slides',
                                                                            1 => 'buttonLink',
                                                                        ],
                                                                    '_identifier' => 'slides.buttonLink',
                                                                ],
                                                        ],
                                                    'maxItems' => 5,
                                                    'minItems' => 1,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'slides',
                                                ],
                                            '_identifier' => 'slides',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'autoplay',
                                            'type' => 'Toggle',
                                            'properties' =>
                                                [
                                                    'default' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'autoplay',
                                                ],
                                            '_identifier' => 'autoplay',
                                        ],
                                ],
                        ],
                ],
            'typo3-contentblocks_example-local' =>
                [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'example-local',
                    'key' => 'example-local',
                    'path' => 'typo3conf/contentBlocks/example-local/',
                    'srcPath' => 'typo3conf/contentBlocks/example-local/src/',
                    'distPath' => 'typo3conf/contentBlocks/example-local/dist/',
                    'icon' => 'typo3conf/contentBlocks/example-local/ContentBlockIcon.svg',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider',
                    'CType' => 'typo3-contentblocks_example-local',
                    'fields' =>
                        [
                            'text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'Default value',
                                            'max' => 15,
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                            'textarea' =>
                                [
                                    'identifier' => 'textarea',
                                    'type' => 'Textarea',
                                    'properties' =>
                                        [
                                            'cols' => 40,
                                            'default' => 'Default value',
                                            'enableRichtext' => true,
                                            'max' => 150,
                                            'placeholder' => 'Placeholder text',
                                            'richtextConfiguration' => 'default',
                                            'rows' => 15,
                                            'required' => false,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'textarea',
                                        ],
                                    '_identifier' => 'textarea',
                                ],
                            'email' =>
                                [
                                    'identifier' => 'email',
                                    'type' => 'Email',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'developer@localhost',
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'email',
                                        ],
                                    '_identifier' => 'email',
                                ],
                            'integer' =>
                                [
                                    'identifier' => 'integer',
                                    'type' => 'Integer',
                                    'properties' =>
                                        [
                                            'default' => 0,
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'integer',
                                        ],
                                    '_identifier' => 'integer',
                                ],
                            'money' =>
                                [
                                    'identifier' => 'money',
                                    'type' => 'Money',
                                    'properties' =>
                                        [
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'money',
                                        ],
                                    '_identifier' => 'money',
                                ],
                            'number' =>
                                [
                                    'identifier' => 'number',
                                    'type' => 'Number',
                                    'properties' =>
                                        [
                                            'default' => 0,
                                            'size' => 20,
                                            'required' => true,
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'number',
                                        ],
                                    '_identifier' => 'number',
                                ],
                            'percent' =>
                                [
                                    'identifier' => 'percent',
                                    'type' => 'Percent',
                                    'properties' =>
                                        [
                                            'default' => 0,
                                            'range' =>
                                                [
                                                    'lower' => 0,
                                                    'upper' => 100,
                                                ],
                                            'required' => true,
                                            'size' => 20,
                                            'slider' =>
                                                [
                                                    'step' => 1,
                                                    'width' => 100,
                                                ],
                                            'trim' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'percent',
                                        ],
                                    '_identifier' => 'percent',
                                ],
                            'url' =>
                                [
                                    'identifier' => 'url',
                                    'type' => 'Url',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 'https://typo3.org',
                                            'linkPopup' =>
                                                [
                                                    'allowedExtensions' => 'pdf',
                                                    'blindLinkFields' => 'target,title',
                                                    'blindLinkOptions' => 'folder,spec,telefone,mail',
                                                ],
                                            'max' => 150,
                                            'placeholder' => 'Placeholder text',
                                            'size' => 20,
                                            'required' => false,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'url',
                                        ],
                                    '_identifier' => 'url',
                                ],
                            'tel' =>
                                [
                                    'identifier' => 'tel',
                                    'type' => 'Tel',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => 0,
                                            'size' => 20,
                                            'required' => false,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'tel',
                                        ],
                                    '_identifier' => 'tel',
                                ],
                            'color' =>
                                [
                                    'identifier' => 'color',
                                    'type' => 'Color',
                                    'properties' =>
                                        [
                                            'autocomplete' => true,
                                            'default' => '#0000aa',
                                            'size' => 5,
                                            'required' => false,
                                            'valuePicker' =>
                                                [
                                                    'items' =>
                                                        [
                                                            '#FF0000' => 'Red',
                                                            '#008000' => 'Green',
                                                            '#0000FF' => 'Blue',
                                                        ],
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'color',
                                        ],
                                    '_identifier' => 'color',
                                ],
                            'date' =>
                                [
                                    'identifier' => 'date',
                                    'type' => 'Date',
                                    'properties' =>
                                        [
                                            'default' => '2020-12-12',
                                            'displayAge' => true,
                                            'size' => 20,
                                            'range' =>
                                                [
                                                    'lower' => '2019-12-12',
                                                    'upper' => '2035-12-12',
                                                ],
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'date',
                                        ],
                                    '_identifier' => 'date',
                                ],
                            'datetime' =>
                                [
                                    'identifier' => 'datetime',
                                    'type' => 'DateTime',
                                    'properties' =>
                                        [
                                            'default' => '2020-01-31 12:00:00',
                                            'displayAge' => true,
                                            'size' => 20,
                                            'range' =>
                                                [
                                                    'lower' => '2019-01-31 12:00:00',
                                                    'upper' => '2040-01-31 12:00:00',
                                                ],
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'datetime',
                                        ],
                                    '_identifier' => 'datetime',
                                ],
                            'time' =>
                                [
                                    'identifier' => 'time',
                                    'type' => 'Time',
                                    'properties' =>
                                        [
                                            'default' => '15:30',
                                            'displayAge' => true,
                                            'size' => 20,
                                            'range' =>
                                                [
                                                    'lower' => '06:01',
                                                    'upper' => '17:59',
                                                ],
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'time',
                                        ],
                                    '_identifier' => 'time',
                                ],
                            'select' =>
                                [
                                    'identifier' => 'select',
                                    'type' => 'Select',
                                    'properties' =>
                                        [
                                            'items' =>
                                                [
                                                    'one' => 'The first',
                                                    'two' => 'The second',
                                                    'three' => 'The third',
                                                ],
                                            'prependLabel' => 'Please choose',
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'select',
                                        ],
                                    '_identifier' => 'select',
                                ],
                            'selectSideBySide' =>
                                [
                                    'identifier' => 'selectSideBySide',
                                    'type' => 'MultiSelect',
                                    'properties' =>
                                        [
                                            'maxItems' => 2,
                                            'size' => 5,
                                            'items' =>
                                                [
                                                    'one' => 'The first',
                                                    'two' => 'The second',
                                                    'three' => 'The third',
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'selectSideBySide',
                                        ],
                                    '_identifier' => 'selectSideBySide',
                                ],
                            'checkboxes' =>
                                [
                                    'identifier' => 'checkboxes',
                                    'type' => 'Checkbox',
                                    'properties' =>
                                        [
                                            'items' =>
                                                [
                                                    'one' => 'The first',
                                                    'two' => 'The second',
                                                    'three' => 'The third',
                                                ],
                                            'default' => 2,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'checkboxes',
                                        ],
                                    '_identifier' => 'checkboxes',
                                ],
                            'radioboxes' =>
                                [
                                    'identifier' => 'radioboxes',
                                    'type' => 'Radiobox',
                                    'properties' =>
                                        [
                                            'default' => 'two',
                                            'items' =>
                                                [
                                                    'one' => 'The first',
                                                    'two' => 'The second',
                                                    'three' => 'The third',
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'radioboxes',
                                        ],
                                    '_identifier' => 'radioboxes',
                                ],
                            'toggle' =>
                                [
                                    'identifier' => 'toggle',
                                    'type' => 'Toggle',
                                    'properties' =>
                                        [
                                            'default' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'toggle',
                                        ],
                                    '_identifier' => 'toggle',
                                ],
                            'toggleInverted' =>
                                [
                                    'identifier' => 'toggleInverted',
                                    'type' => 'Toggle',
                                    'properties' =>
                                        [
                                            'invertStateDisplay' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'toggleInverted',
                                        ],
                                    '_identifier' => 'toggleInverted',
                                ],
                            'image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    '_path' =>
                                        [
                                            0 => 'image',
                                        ],
                                    '_identifier' => 'image',
                                ],
                            'bodytext' =>
                                [
                                    'identifier' => 'bodytext',
                                    'type' => 'Textarea',
                                    'properties' =>
                                        [
                                            'useExistingField' => true,
                                            'enableRichtext' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'bodytext',
                                        ],
                                    '_identifier' => 'bodytext',
                                ],
                            'collection' =>
                                [
                                    'identifier' => 'collection',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'useAsLabel' => 'text',
                                            'maxItems' => 5,
                                            'required' => true,
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'text',
                                                                ],
                                                            '_identifier' => 'collection.text',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'collection',
                                                            'type' => 'Collection',
                                                            'properties' =>
                                                                [
                                                                    'maxItems' => 2,
                                                                    'minItems' => 1,
                                                                    'fields' =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'identifier' => 'text',
                                                                                    'type' => 'Text',
                                                                                    '_path' =>
                                                                                        [
                                                                                            0 => 'collection',
                                                                                            1 => 'collection',
                                                                                            2 => 'text',
                                                                                        ],
                                                                                    '_identifier' => 'collection.collection.text',
                                                                                ],
                                                                            1 =>
                                                                                [
                                                                                    'identifier' => 'cb_slider_local_slides_text',
                                                                                    'type' => 'Textarea',
                                                                                    'properties' =>
                                                                                        [
                                                                                            'useExistingField' => true,
                                                                                            'enableRichtext' => true,
                                                                                        ],
                                                                                    '_path' =>
                                                                                        [
                                                                                            0 => 'collection',
                                                                                            1 => 'collection',
                                                                                            2 => 'cb_slider_local_slides_text',
                                                                                        ],
                                                                                    '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                ],
                                                                        ],
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                ],
                                                            '_identifier' => 'collection.collection',
                                                        ],
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                        ],
                                    '_identifier' => 'collection',
                                ],
                            'collection.text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                            1 => 'text',
                                        ],
                                    '_identifier' => 'collection.text',
                                ],
                            'collection.collection' =>
                                [
                                    'identifier' => 'collection',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'maxItems' => 2,
                                            'minItems' => 1,
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                    2 => 'text',
                                                                ],
                                                            '_identifier' => 'collection.collection.text',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'cb_slider_local_slides_text',
                                                            'type' => 'Textarea',
                                                            'properties' =>
                                                                [
                                                                    'useExistingField' => true,
                                                                    'enableRichtext' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                    2 => 'cb_slider_local_slides_text',
                                                                ],
                                                            '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                        ],
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                            1 => 'collection',
                                        ],
                                    '_identifier' => 'collection.collection',
                                ],
                            'collection.collection.text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                            1 => 'collection',
                                            2 => 'text',
                                        ],
                                    '_identifier' => 'collection.collection.text',
                                ],
                            'collection.collection.cb_slider_local_slides_text' =>
                                [
                                    'identifier' => 'cb_slider_local_slides_text',
                                    'type' => 'Textarea',
                                    'properties' =>
                                        [
                                            'useExistingField' => true,
                                            'enableRichtext' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                            1 => 'collection',
                                            2 => 'cb_slider_local_slides_text',
                                        ],
                                    '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                ],
                        ],
                    'collectionFields' =>
                        [
                            'collection' =>
                                [
                                    'identifier' => 'collection',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'useAsLabel' => 'text',
                                            'maxItems' => 5,
                                            'required' => true,
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'text',
                                                                ],
                                                            '_identifier' => 'collection.text',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'collection',
                                                            'type' => 'Collection',
                                                            'properties' =>
                                                                [
                                                                    'maxItems' => 2,
                                                                    'minItems' => 1,
                                                                    'fields' =>
                                                                        [
                                                                            0 =>
                                                                                [
                                                                                    'identifier' => 'text',
                                                                                    'type' => 'Text',
                                                                                    '_path' =>
                                                                                        [
                                                                                            0 => 'collection',
                                                                                            1 => 'collection',
                                                                                            2 => 'text',
                                                                                        ],
                                                                                    '_identifier' => 'collection.collection.text',
                                                                                ],
                                                                            1 =>
                                                                                [
                                                                                    'identifier' => 'cb_slider_local_slides_text',
                                                                                    'type' => 'Textarea',
                                                                                    'properties' =>
                                                                                        [
                                                                                            'useExistingField' => true,
                                                                                            'enableRichtext' => true,
                                                                                        ],
                                                                                    '_path' =>
                                                                                        [
                                                                                            0 => 'collection',
                                                                                            1 => 'collection',
                                                                                            2 => 'cb_slider_local_slides_text',
                                                                                        ],
                                                                                    '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                ],
                                                                        ],
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                ],
                                                            '_identifier' => 'collection.collection',
                                                        ],
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                        ],
                                    '_identifier' => 'collection',
                                ],
                            'collection.collection' =>
                                [
                                    'identifier' => 'collection',
                                    'type' => 'Collection',
                                    'properties' =>
                                        [
                                            'maxItems' => 2,
                                            'minItems' => 1,
                                            'fields' =>
                                                [
                                                    0 =>
                                                        [
                                                            'identifier' => 'text',
                                                            'type' => 'Text',
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                    2 => 'text',
                                                                ],
                                                            '_identifier' => 'collection.collection.text',
                                                        ],
                                                    1 =>
                                                        [
                                                            'identifier' => 'cb_slider_local_slides_text',
                                                            'type' => 'Textarea',
                                                            'properties' =>
                                                                [
                                                                    'useExistingField' => true,
                                                                    'enableRichtext' => true,
                                                                ],
                                                            '_path' =>
                                                                [
                                                                    0 => 'collection',
                                                                    1 => 'collection',
                                                                    2 => 'cb_slider_local_slides_text',
                                                                ],
                                                            '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                        ],
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'collection',
                                            1 => 'collection',
                                        ],
                                    '_identifier' => 'collection.collection',
                                ],
                        ],
                    'fileFields' =>
                        [
                            'image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    '_path' =>
                                        [
                                            0 => 'image',
                                        ],
                                    '_identifier' => 'image',
                                ],
                        ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/example-local/src',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/example-local/src/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/example-local/src/Layouts',
                    'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/example-local/src/EditorPreview.html',
                    'EditorInterfaceXlf' => 'typo3conf/contentBlocks/example-local/src/Language/EditorInterface.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/example-local/src/Language/EditorInterface.xlf:typo3-contentblocks.example-local',
                    'FrontendXlf' => 'typo3conf/contentBlocks/example-local/src/Language/Frontend.xlf',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/example-local/src/Language/Frontend.xlf:typo3-contentblocks.example-local',
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Text',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 'Default value',
                                                    'max' => 15,
                                                    'placeholder' => 'Placeholder text',
                                                    'size' => 20,
                                                    'required' => false,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'text',
                                                ],
                                            '_identifier' => 'text',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'textarea',
                                            'type' => 'Textarea',
                                            'properties' =>
                                                [
                                                    'cols' => 40,
                                                    'default' => 'Default value',
                                                    'enableRichtext' => true,
                                                    'max' => 150,
                                                    'placeholder' => 'Placeholder text',
                                                    'richtextConfiguration' => 'default',
                                                    'rows' => 15,
                                                    'required' => false,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'textarea',
                                                ],
                                            '_identifier' => 'textarea',
                                        ],
                                    2 =>
                                        [
                                            'identifier' => 'email',
                                            'type' => 'Email',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 'developer@localhost',
                                                    'placeholder' => 'Placeholder text',
                                                    'size' => 20,
                                                    'required' => true,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'email',
                                                ],
                                            '_identifier' => 'email',
                                        ],
                                    3 =>
                                        [
                                            'identifier' => 'integer',
                                            'type' => 'Integer',
                                            'properties' =>
                                                [
                                                    'default' => 0,
                                                    'size' => 20,
                                                    'required' => true,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'integer',
                                                ],
                                            '_identifier' => 'integer',
                                        ],
                                    4 =>
                                        [
                                            'identifier' => 'money',
                                            'type' => 'Money',
                                            'properties' =>
                                                [
                                                    'size' => 20,
                                                    'required' => true,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'money',
                                                ],
                                            '_identifier' => 'money',
                                        ],
                                    5 =>
                                        [
                                            'identifier' => 'number',
                                            'type' => 'Number',
                                            'properties' =>
                                                [
                                                    'default' => 0,
                                                    'size' => 20,
                                                    'required' => true,
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'number',
                                                ],
                                            '_identifier' => 'number',
                                        ],
                                    6 =>
                                        [
                                            'identifier' => 'percent',
                                            'type' => 'Percent',
                                            'properties' =>
                                                [
                                                    'default' => 0,
                                                    'range' =>
                                                        [
                                                            'lower' => 0,
                                                            'upper' => 100,
                                                        ],
                                                    'required' => true,
                                                    'size' => 20,
                                                    'slider' =>
                                                        [
                                                            'step' => 1,
                                                            'width' => 100,
                                                        ],
                                                    'trim' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'percent',
                                                ],
                                            '_identifier' => 'percent',
                                        ],
                                    7 =>
                                        [
                                            'identifier' => 'url',
                                            'type' => 'Url',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 'https://typo3.org',
                                                    'linkPopup' =>
                                                        [
                                                            'allowedExtensions' => 'pdf',
                                                            'blindLinkFields' => 'target,title',
                                                            'blindLinkOptions' => 'folder,spec,telefone,mail',
                                                        ],
                                                    'max' => 150,
                                                    'placeholder' => 'Placeholder text',
                                                    'size' => 20,
                                                    'required' => false,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'url',
                                                ],
                                            '_identifier' => 'url',
                                        ],
                                    8 =>
                                        [
                                            'identifier' => 'tel',
                                            'type' => 'Tel',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => 0,
                                                    'size' => 20,
                                                    'required' => false,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'tel',
                                                ],
                                            '_identifier' => 'tel',
                                        ],
                                    9 =>
                                        [
                                            'identifier' => 'color',
                                            'type' => 'Color',
                                            'properties' =>
                                                [
                                                    'autocomplete' => true,
                                                    'default' => '#0000aa',
                                                    'size' => 5,
                                                    'required' => false,
                                                    'valuePicker' =>
                                                        [
                                                            'items' =>
                                                                [
                                                                    '#FF0000' => 'Red',
                                                                    '#008000' => 'Green',
                                                                    '#0000FF' => 'Blue',
                                                                ],
                                                        ],
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'color',
                                                ],
                                            '_identifier' => 'color',
                                        ],
                                    10 =>
                                        [
                                            'identifier' => 'date',
                                            'type' => 'Date',
                                            'properties' =>
                                                [
                                                    'default' => '2020-12-12',
                                                    'displayAge' => true,
                                                    'size' => 20,
                                                    'range' =>
                                                        [
                                                            'lower' => '2019-12-12',
                                                            'upper' => '2035-12-12',
                                                        ],
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'date',
                                                ],
                                            '_identifier' => 'date',
                                        ],
                                    11 =>
                                        [
                                            'identifier' => 'datetime',
                                            'type' => 'DateTime',
                                            'properties' =>
                                                [
                                                    'default' => '2020-01-31 12:00:00',
                                                    'displayAge' => true,
                                                    'size' => 20,
                                                    'range' =>
                                                        [
                                                            'lower' => '2019-01-31 12:00:00',
                                                            'upper' => '2040-01-31 12:00:00',
                                                        ],
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'datetime',
                                                ],
                                            '_identifier' => 'datetime',
                                        ],
                                    12 =>
                                        [
                                            'identifier' => 'time',
                                            'type' => 'Time',
                                            'properties' =>
                                                [
                                                    'default' => '15:30',
                                                    'displayAge' => true,
                                                    'size' => 20,
                                                    'range' =>
                                                        [
                                                            'lower' => '06:01',
                                                            'upper' => '17:59',
                                                        ],
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'time',
                                                ],
                                            '_identifier' => 'time',
                                        ],
                                    13 =>
                                        [
                                            'identifier' => 'select',
                                            'type' => 'Select',
                                            'properties' =>
                                                [
                                                    'items' =>
                                                        [
                                                            'one' => 'The first',
                                                            'two' => 'The second',
                                                            'three' => 'The third',
                                                        ],
                                                    'prependLabel' => 'Please choose',
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'select',
                                                ],
                                            '_identifier' => 'select',
                                        ],
                                    14 =>
                                        [
                                            'identifier' => 'selectSideBySide',
                                            'type' => 'MultiSelect',
                                            'properties' =>
                                                [
                                                    'maxItems' => 2,
                                                    'size' => 5,
                                                    'items' =>
                                                        [
                                                            'one' => 'The first',
                                                            'two' => 'The second',
                                                            'three' => 'The third',
                                                        ],
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'selectSideBySide',
                                                ],
                                            '_identifier' => 'selectSideBySide',
                                        ],
                                    15 =>
                                        [
                                            'identifier' => 'checkboxes',
                                            'type' => 'Checkbox',
                                            'properties' =>
                                                [
                                                    'items' =>
                                                        [
                                                            'one' => 'The first',
                                                            'two' => 'The second',
                                                            'three' => 'The third',
                                                        ],
                                                    'default' => 2,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'checkboxes',
                                                ],
                                            '_identifier' => 'checkboxes',
                                        ],
                                    16 =>
                                        [
                                            'identifier' => 'radioboxes',
                                            'type' => 'Radiobox',
                                            'properties' =>
                                                [
                                                    'default' => 'two',
                                                    'items' =>
                                                        [
                                                            'one' => 'The first',
                                                            'two' => 'The second',
                                                            'three' => 'The third',
                                                        ],
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'radioboxes',
                                                ],
                                            '_identifier' => 'radioboxes',
                                        ],
                                    17 =>
                                        [
                                            'identifier' => 'toggle',
                                            'type' => 'Toggle',
                                            'properties' =>
                                                [
                                                    'default' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'toggle',
                                                ],
                                            '_identifier' => 'toggle',
                                        ],
                                    18 =>
                                        [
                                            'identifier' => 'toggleInverted',
                                            'type' => 'Toggle',
                                            'properties' =>
                                                [
                                                    'invertStateDisplay' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'toggleInverted',
                                                ],
                                            '_identifier' => 'toggleInverted',
                                        ],
                                    19 =>
                                        [
                                            'identifier' => 'image',
                                            'type' => 'Image',
                                            '_path' =>
                                                [
                                                    0 => 'image',
                                                ],
                                            '_identifier' => 'image',
                                        ],
                                    20 =>
                                        [
                                            'identifier' => 'bodytext',
                                            'type' => 'Textarea',
                                            'properties' =>
                                                [
                                                    'useExistingField' => true,
                                                    'enableRichtext' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'bodytext',
                                                ],
                                            '_identifier' => 'bodytext',
                                        ],
                                    21 =>
                                        [
                                            'identifier' => 'collection',
                                            'type' => 'Collection',
                                            'properties' =>
                                                [
                                                    'useAsLabel' => 'text',
                                                    'maxItems' => 5,
                                                    'required' => true,
                                                    'fields' =>
                                                        [
                                                            0 =>
                                                                [
                                                                    'identifier' => 'text',
                                                                    'type' => 'Text',
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'collection',
                                                                            1 => 'text',
                                                                        ],
                                                                    '_identifier' => 'collection.text',
                                                                ],
                                                            1 =>
                                                                [
                                                                    'identifier' => 'collection',
                                                                    'type' => 'Collection',
                                                                    'properties' =>
                                                                        [
                                                                            'maxItems' => 2,
                                                                            'minItems' => 1,
                                                                            'fields' =>
                                                                                [
                                                                                    0 =>
                                                                                        [
                                                                                            'identifier' => 'text',
                                                                                            'type' => 'Text',
                                                                                            '_path' =>
                                                                                                [
                                                                                                    0 => 'collection',
                                                                                                    1 => 'collection',
                                                                                                    2 => 'text',
                                                                                                ],
                                                                                            '_identifier' => 'collection.collection.text',
                                                                                        ],
                                                                                    1 =>
                                                                                        [
                                                                                            'identifier' => 'cb_slider_local_slides_text',
                                                                                            'type' => 'Textarea',
                                                                                            'properties' =>
                                                                                                [
                                                                                                    'useExistingField' => true,
                                                                                                    'enableRichtext' => true,
                                                                                                ],
                                                                                            '_path' =>
                                                                                                [
                                                                                                    0 => 'collection',
                                                                                                    1 => 'collection',
                                                                                                    2 => 'cb_slider_local_slides_text',
                                                                                                ],
                                                                                            '_identifier' => 'collection.collection.cb_slider_local_slides_text',
                                                                                        ],
                                                                                ],
                                                                        ],
                                                                    '_path' =>
                                                                        [
                                                                            0 => 'collection',
                                                                            1 => 'collection',
                                                                        ],
                                                                    '_identifier' => 'collection.collection',
                                                                ],
                                                        ],
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'collection',
                                                ],
                                            '_identifier' => 'collection',
                                        ],
                                ],
                        ],
                ],
            'typo3-contentblocks_counter-local' =>
                [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'counter-local',
                    'key' => 'counter-local',
                    'path' => 'typo3conf/contentBlocks/counter-local/',
                    'srcPath' => 'typo3conf/contentBlocks/counter-local/src/',
                    'distPath' => 'typo3conf/contentBlocks/counter-local/dist/',
                    'icon' => 'typo3conf/contentBlocks/counter-local/ContentBlockIcon.png',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\BitmapIconProvider',
                    'CType' => 'typo3-contentblocks_counter-local',
                    'fields' =>
                        [
                            'amount' =>
                                [
                                    'identifier' => 'amount',
                                    'type' => 'Number',
                                    'properties' =>
                                        [
                                            'defaultValue' => 50,
                                            'range' =>
                                                [
                                                    'lower' => 26,
                                                    'upper' => 112,
                                                ],
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'amount',
                                        ],
                                    '_identifier' => 'amount',
                                ],
                            'icon' =>
                                [
                                    'identifier' => 'icon',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'allowedExtensions' => 'svg',
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'icon',
                                        ],
                                    '_identifier' => 'icon',
                                ],
                            'text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                        ],
                    'collectionFields' =>
                        [
                        ],
                    'fileFields' =>
                        [
                            'icon' =>
                                [
                                    'identifier' => 'icon',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'allowedExtensions' => 'svg',
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                            'required' => true,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'icon',
                                        ],
                                    '_identifier' => 'icon',
                                ],
                        ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/counter-local/src',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/counter-local/src/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/counter-local/src/Layouts',
                    'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/counter-local/src/EditorPreview.html',
                    'EditorInterfaceXlf' => 'typo3conf/contentBlocks/counter-local/src/Language/EditorInterface.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/counter-local/src/Language/EditorInterface.xlf:typo3-contentblocks.counter-local',
                    'FrontendXlf' => 'typo3conf/contentBlocks/counter-local/src/Language/Frontend.xlf',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/counter-local/src/Language/Frontend.xlf:typo3-contentblocks.counter-local',
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'amount',
                                            'type' => 'Number',
                                            'properties' =>
                                                [
                                                    'defaultValue' => 50,
                                                    'range' =>
                                                        [
                                                            'lower' => 26,
                                                            'upper' => 112,
                                                        ],
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'amount',
                                                ],
                                            '_identifier' => 'amount',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'icon',
                                            'type' => 'Image',
                                            'properties' =>
                                                [
                                                    'allowedExtensions' => 'svg',
                                                    'minItems' => 1,
                                                    'maxItems' => 1,
                                                    'required' => true,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'icon',
                                                ],
                                            '_identifier' => 'icon',
                                        ],
                                    2 =>
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Text',
                                            '_path' =>
                                                [
                                                    0 => 'text',
                                                ],
                                            '_identifier' => 'text',
                                        ],
                                ],
                        ],
                ],
            'typo3-contentblocks_call-to-action-local' =>
                [
                    '__warning' => 'Contents of this "cb" configuration are not API yet and might change!',
                    'vendor' => 'typo3-contentblocks',
                    'package' => 'call-to-action-local',
                    'key' => 'call-to-action-local',
                    'path' => 'typo3conf/contentBlocks/call-to-action-local/',
                    'srcPath' => 'typo3conf/contentBlocks/call-to-action-local/src/',
                    'distPath' => 'typo3conf/contentBlocks/call-to-action-local/dist/',
                    'icon' => 'typo3conf/contentBlocks/call-to-action-local/ContentBlockIcon.svg',
                    'iconProviderClass' => 'TYPO3\\CMS\\Core\\Imaging\\IconProvider\\SvgIconProvider',
                    'CType' => 'typo3-contentblocks_call-to-action-local',
                    'fields' =>
                        [
                            'image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'image',
                                        ],
                                    '_identifier' => 'image',
                                ],
                            'text' =>
                                [
                                    'identifier' => 'text',
                                    'type' => 'Textarea',
                                    'properties' =>
                                        [
                                            'enableRichtext' => true,
                                            'richtextConfiguration' => 'default',
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'text',
                                        ],
                                    '_identifier' => 'text',
                                ],
                            'buttonText' =>
                                [
                                    'identifier' => 'buttonText',
                                    'type' => 'Text',
                                    '_path' =>
                                        [
                                            0 => 'buttonText',
                                        ],
                                    '_identifier' => 'buttonText',
                                ],
                            'link' =>
                                [
                                    'identifier' => 'link',
                                    'type' => 'Url',
                                    'properties' =>
                                        [
                                            'linkTypes' =>
                                                [
                                                    0 => 'page',
                                                    1 => 'external',
                                                ],
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'link',
                                        ],
                                    '_identifier' => 'link',
                                ],
                        ],
                    'collectionFields' =>
                        [
                        ],
                    'fileFields' =>
                        [
                            'image' =>
                                [
                                    'identifier' => 'image',
                                    'type' => 'Image',
                                    'properties' =>
                                        [
                                            'minItems' => 1,
                                            'maxItems' => 1,
                                        ],
                                    '_path' =>
                                        [
                                            0 => 'image',
                                        ],
                                    '_identifier' => 'image',
                                ],
                        ],
                    'frontendTemplatesPath' => 'typo3conf/contentBlocks/call-to-action-local/src',
                    'frontendPartialsPath' => 'typo3conf/contentBlocks/call-to-action-local/src/Partials',
                    'frontendLayoutsPath' => 'typo3conf/contentBlocks/call-to-action-local/src/Layouts',
                    'EditorPreview.html' => '/var/www/html/.typo3/public/typo3conf/contentBlocks/call-to-action-local/src/EditorPreview.html',
                    'EditorInterfaceXlf' => 'typo3conf/contentBlocks/call-to-action-local/src/Language/Default.xlf',
                    'EditorLLL' => 'LLL:typo3conf/contentBlocks/call-to-action-local/src/Language/Default.xlf:typo3-contentblocks.call-to-action-local',
                    'FrontendXlf' => 'typo3conf/contentBlocks/call-to-action-local/src/Language/Default.xlf',
                    'FrontendLLL' => 'LLL:typo3conf/contentBlocks/call-to-action-local/src/Language/Default.xlf:typo3-contentblocks.call-to-action-local',
                    'yaml' =>
                        [
                            'group' => 'common',
                            'fields' =>
                                [
                                    0 =>
                                        [
                                            'identifier' => 'image',
                                            'type' => 'Image',
                                            'properties' =>
                                                [
                                                    'minItems' => 1,
                                                    'maxItems' => 1,
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'image',
                                                ],
                                            '_identifier' => 'image',
                                        ],
                                    1 =>
                                        [
                                            'identifier' => 'text',
                                            'type' => 'Textarea',
                                            'properties' =>
                                                [
                                                    'enableRichtext' => true,
                                                    'richtextConfiguration' => 'default',
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'text',
                                                ],
                                            '_identifier' => 'text',
                                        ],
                                    2 =>
                                        [
                                            'identifier' => 'buttonText',
                                            'type' => 'Text',
                                            '_path' =>
                                                [
                                                    0 => 'buttonText',
                                                ],
                                            '_identifier' => 'buttonText',
                                        ],
                                    3 =>
                                        [
                                            'identifier' => 'link',
                                            'type' => 'Url',
                                            'properties' =>
                                                [
                                                    'linkTypes' =>
                                                        [
                                                            0 => 'page',
                                                            1 => 'external',
                                                        ],
                                                ],
                                            '_path' =>
                                                [
                                                    0 => 'link',
                                                ],
                                            '_identifier' => 'link',
                                        ],
                                ],
                        ],
                ],
        ];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @param array<ContentBlockConfiguration> $cbs
     * @return array<ContentBlockConfiguration>
     */
    protected function enrichContentblocksForBackend(array $cbs): array
    {
        // @todo use a factory to generate DTO(s)
        foreach ($cbs as &$cb) {
            $cb->static = [
                'cType' => $cb->getCType(),
                'title' => $this->getLanguageService()->sL($cb->editorLLL . '.title'),
            ];

            $cb->fieldsConfig = $this->enrichFieldsForBackend($cb->fieldsConfig, $cb);
        }

        return $cbs;
    }

    /**
     * @param array<AbstractFieldConfiguration> $fieldsConfig
     * @return array<AbstractFieldConfiguration>
     */
    protected function enrichFieldsForBackend(array $fieldsConfig, ContentBlockConfiguration $cb): array
    {
        return array_map(
            function (AbstractFieldConfiguration $fieldConfig) use ($cb) {
                if ($fieldConfig->type == FieldType::COLLECTION) {
                    /*
                     * @todo: is_a(...)
                     */
                    /** @var CollectionFieldConfiguration $fieldConfig- >fields */
                    $fieldConfig->fields = $this->enrichFieldsForBackend($fieldConfig->fields, $cb);
                }
                $fieldConfig->static = [
                    'title' => $this->getLanguageService()->sL(
                    // @todo: should be uniqueIdentifier
                        $cb->editorLLL . '.' . $fieldConfig->identifier
                    ),
                ];

                return $fieldConfig;
            },
            $fieldsConfig
        );
    }
}
