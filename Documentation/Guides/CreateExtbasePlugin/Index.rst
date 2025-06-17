.. include:: /Includes.rst.txt
.. _create-extbase-plugin:

==========
Create Extbase Plugins
==========

This guide demonstrates how to create Extbase plugins using Content Blocks.

Create a new Content Block
===================================

.. code-block:: yaml
   :caption: EXT:site_package/ContentBlocks/ContentElements/Artists/config.yaml

    name: vendor/artists-list
    group: plugins


.. code-block:: html
   :caption: EXT:site_package/ContentBlocks/ContentElements/Artists/templates/frontend.html

    <f:cObject typoscriptObjectPath="{data.mainType}.{data.recordType}.20" table="{data.mainType}" data="{data:data}"/>

Add TypoScript
===================================

For `extensionName` and `pluginName` use the names as configured in the next setp.

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    tt_content.vendor_artistslist.20 = EXTBASEPLUGIN
    tt_content.vendor_artistslist.20 {
        extensionName = MyExtension
        pluginName = MyPlugin
        # If your controller returns a HTML response, set the template path like this:
        view {
            templateRootPaths.0 = EXT:site_package/ContentBlocks/ContentElements/Artists/templates
        }
    }

Register Controller Actions
===================================

.. code-block:: php
   :caption: EXT:site_package/ext_localconf.php

    ExtensionUtility::registerControllerActions(
        'MyExtension',
        'MyPlugin',
        [
            ArtistController::class => ['list']
        ],
        [
            ArtistController::class => []
        ]
    );

.. note::
    **Why not just use configurePlugin / registerPlugin?**

    TYPO3 has a helper method to quickly create plugins in ExtensionUtility::configurePlugin/registerPlugin

    In the background, it will create a completely new Content Element, which just copies the "Header" element.
    If you need custom fields or FlexForm configuration, you need to manually override the element.
    But if you create the Content Element with Content Blocks and only register the controller actions
    for it via the utility, you have the full power of Content Blocks on your side. You can quickly create
    FlexForm config and manage labels, icons etc. in your component.

.. note::
    registerControllerActions is an internal method, but it is unlikely to change in version 13. This way of adding plugins is experimental.

Example Controller
===================================

This controller simply passes the data from your Content Block to the Fluid template.

.. code-block:: php
   :caption: EXT:site_package/Classes/Controller/ArtistController.php

    <?php

    declare(strict_types=1);

    namespace Vendor\MyVendor\Controller;

    use Psr\Http\Message\ResponseInterface;
    use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

    class ArtistController extends ActionController
    {
        public function listAction(): ResponseInterface
        {
            /** @var ContentObjectRenderer $contentObject */
            $contentObject = $this->request->getAttribute('currentContentObject');
            $dataFromTypoScript = $contentObject->data;

            $this->view->assign('data', $dataFromTypoScript['data']);

            return $this->htmlResponse();
        }
    }


See also:

*  :ref:`Extbase Plugins <t3tsref:cobj-extbaseplugin>`
