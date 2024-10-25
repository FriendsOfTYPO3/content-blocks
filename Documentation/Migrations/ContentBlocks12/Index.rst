.. include:: /Includes.rst.txt
.. _migrations-content-blocks-12:

======================
Content Blocks for v12
======================

With the release of Content Blocks `1.0.0 <https://github.com/FriendsOfTYPO3/content-blocks/releases/tag/1.0.0>`__
some things changed under the hood, which require migration in your Fluid
templates. Also, the folder structure changed as well as usage of ViewHelpers.

New composer name
=================

The composer name has changed.

`contentblocks/content-blocks` ➡️ `friendsoftypo3/content-blocks`

Please migrate, as the old package is abandoned.

New folder structure
====================

There is a migration wizard to rename your Content Blocks folders and files to
the :ref:`new structure <cb_definition>`.

.. code-block:: shell

    typo3 upgrade:run contentBlocksFolderStructureMigration

Ensure the extension with the old Content Block structure is loaded in the
system before running this wizard.

New AssetPathViewHelper
=======================

We replaced the custom AssetViewHelpers with a new
:ref:`AssetPathViewHelper <asset_view_helpers>`. Now you can use the Core
AssetViewHelpers and only use the custom ViewHelpers to build the path to your
asset.

.. code-block:: html

    <!-- Before -->
    <cb:asset.css identifier="cbAccordionCssBackend" file="EditorPreview.css"/>

    <!-- After -->
    <f:asset.css identifier="cbAccordionCssBackend" href="{cb:assetPath()}/EditorPreview.css"/>

New LanguagePathViewHelper
==========================

We replaced the custom TranslateViewHelper with a new
:ref:`LanguagePathViewHelper <language_path_view_helper>` that is used to build
the translation key.

.. code-block:: html

    <!-- Before -->
    <cb:translate key="readmore"/>

    <!-- After -->
    <f:translate key="{cb:languagePath()}:readmore"/>

Record object
=============

Content Blocks now uses the :php:`\TYPO3\CMS\Core\Domain\Record` under the hood.
This has changed how some record attributes are accessed.

*  `{data.typeName}` ➡️ `{data.recordType}`
*  `{data.tableName}` ➡️ `{data.mainType}`
*  `{data.creationDate}` ➡️ `{data.systemProperties.createdAt}`
*  `{data.updateDate}` ➡️ `{data.systemProperties.lastUpdatedAt}`
*  `{data.localizedUid}` ➡️ `{data.computedProperties.localizedUid}`

Data processing
===============

Content Blocks now uses the :php:`\TYPO3\CMS\Frontend\DataProcessing\RecordTransformationProcessor`
under the hood. This has changed how some fields are transformed.

Link
----

The type :ref:`Link <field_type_link>` field will now resolve to an object of
type :php:`\TYPO3\CMS\Core\LinkHandling\TypolinkParameter`. Checks for existence
need to be adjusted to check for the :html:`url` property instead.

.. code-block:: html

    <!-- Before -->
    <f:if condition="{data.link_field}">
        <!-- -->
    </f:if>


    <!-- After -->
    <f:if condition="{data.link_field.url}">
        <!-- -->
    </f:if>

Folder
------

The type :ref:`Folder <field_type_link>` field will now resolve to a list of
:php:`\TYPO3\CMS\Core\Resource\Folder` objects.

.. code-block:: html

    <!-- Before -->
    <f:for each="{data.folder}" as="folder">
        <f:for each="{folder}" as="image">
            <f:image image="{item}" />
        </f:for>
    </f:for>


    <!-- After -->
    <f:for each="{data.folder}" as="folder">
        <f:for each="{folder.files}" as="image">
            <f:image image="{item}" />
        </f:for>
    </f:for>

FlexForm
--------

New: Sub-fields of type FlexForm are now resolved as well.

Groups
======

The property :yaml:`group` now works for both the `NewContentElementWizard` and
for the record selector in the edit view. With this, the way to register groups
has changed.

.. note::

    The group :yaml:`common` was renamed to :yaml:`default`.

Before:

.. code-block:: typoscript
   :caption: EXT:my_package/Configuration/page.tsconfig

    mod.wizards.newContentElement.wizardItems {
        my_group {
            header = LLL:EXT:my_package/Resources/Private/Language/Backend.xlf:content_group.my_group
            before = common
        }
    }

After:

.. code-block:: php
   :caption: EXT:my_package/Configuration/TCA/Overrides/tt_content.php

    <?php

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItemGroup(
        'tt_content', // table
        'CType', // typeField
        'my_group', // group
        'LLL:EXT:my_package/Resources/Private/Language/Backend.xlf:content_group.my_group', // label
        'before:default', // position
    );

Public assets
=============

The `assets` folder of your Content Block is now symlinked to the extension's
`Resources/Public/ContentBlocks/*` folder. You should add this folder to your
.gitignore file.
