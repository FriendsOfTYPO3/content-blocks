.. include:: /Includes.rst.txt
.. _changelog-1.3:

===
1.3
===

..  contents::

Features
========

Restrict Collection child types
-------------------------------

It is now possible to set specific record types, which should be available for
the type selector of Collection items. The most common use case is probably some
kind of accordion or tab element, which should only have a few allowed Content
Elements as children.

Example
_______

In this example only the Core types `text` and `images` are allowed in
:yaml:`allowedRecordTypes` inside the Collection field.

.. code-block:: yaml

    name: example/tabs
    fields:
      - identifier: header
        useExistingField: true
      - identifier: tabs_item
        type: Collection
        minitems: 1
        foreign_table: tt_content
        allowedRecordTypes:
          - text
          - images

If you've used Mask before, you know this feature. The Content Blocks
implementation is based on the FormDataProvider API in contrast to Mask, which
used itemsProcFunc.

Link EditRecord ViewHelper
--------------------------

Content Blocks introduces its own :html:`cb:link.editRecord` ViewHelper. In
contrast to the Core :html:`be:link.editRecord` ViewHelper it has support for
Page Layout anchor links by default. This means when you click on a custom edit
link and close the editing interface again, you will automatically jump to the
referring Content Element.

.. note::

    This new ViewHelper is only useful in the Page Layout context for Content
    Element previews. For all other cases using the Core
    :html:`be:link.editRecord` is still valid.

Example 1: Content Element
__________________________

Here the bodytext of the Content Element is linked.

.. code-block:: html

    <html
        xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
        xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"
        data-namespace-typo3-fluid="true"
    >

    <f:layout name="Preview"/>

    <f:section name="Header"/>

    <f:section name="Content">
        <f:asset.css identifier="cbCtaCssBackend" href="{cb:assetPath()}/preview.css"/>
        <div class="cb-cta">
            <h2>{data.header}</h2>
            <cb:link.editRecord record="{data}">
                <f:format.raw>{data.bodytext}</f:format.raw>
            </cb:link.editRecord>
        </div>
    </f:section>

    </html>

Example 2: Linking Collections
______________________________

If you have a custom preview for your Collection, you can link the items
individually, making it easier for editors to edit one specific item quickly.
The important part is to add the `id` attribute following the schema:
`element-{table}-{uid}`. In this example the text of an accordion item is linked
this way.


.. code-block:: html

    <html
        xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
        xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"
        data-namespace-typo3-fluid="true"
    >

    <f:layout name="Preview"/>

    <f:section name="Content">
        <f:asset.css identifier="cbAccordionCssBackend" href="{cb:assetPath()}/preview.css"/>
        <div class="cb-accordion">
            <f:for each="{data.accordion_item}" as="item">
                <cb:link.editRecord record="{item}">
                    <div id="element-{item.mainType}-{item.uid}" class="accordion-item">
                        {item.text}
                    </div>
                </cb:link.editRecord>
            </f:for>
        </div>
    </f:section>

    </html>

Migration
_________

If you already used the :html:`be:link.editRecord` ViewHelper, just make a quick
search and replace: `<be:link.editRecord` -> `<cb:link.editRecord` and
`</be:link.editRecord>` -> `</cb:link.editRecord>`. The old parameters `uid` and
`table` still work as a fallback. After that, you can also remove the namespace
import in case you didn't use any other ViewHelper in the `be` namespace.
Importing the `cb` namespace it optional as it is registered globally.

Default config
--------------

The content-blocks.yaml file has been extended to support arbitrary default
values for the generated config.yaml file from the make:content-block command.

Example
_______

To use this new feature, create a content-blocks.yaml file in the root directory
of your project. Then, add a :yaml:`config` key followed by a Content Type
identifier. In this case, we set default values for Content Elements, so we use
:yaml:`content-element`. You can also set default values for `page-type`,
`record-type` or `file-type`. Values defined in here override the generated
configuration by the command.

.. code-block:: yaml

    config:
      content-element:
        basics:
          - TYPO3/Appearance
          - TYPO3/Links
        group: my_group
        prefixFields: true
        prefixType: vendor

Support for PAGEVIEW
--------------------

The Content Blocks Data Processor is now able to resolve the Page Record for
page templates based on :typoscript:`PAGEVIEW`.

Example
_______

This will add the variable :html:`data` to your page template which contains the
resolved page record. This also works for Page Types not defined by Content
Blocks like the default Page Type "1".

.. code-block:: typoscript

    page = PAGE
    page.10 = PAGEVIEW
    page.10 {
      paths.10 = EXT:content_blocks_examples/Resources/Private/Templates
      dataProcessing {
        10 = page-content
        20 = content-blocks
      }
    }

Core Types resolved to Record Objects
-------------------------------------

Cre Types are now also transformed to Record Objects by the Content Blocks Data
Processor. This unifies the experience for users who has both Core defined and
Content Blocks defined Types in their installation.

.. note::

    Before Content Blocks 1.3 the Content Blocks Data Processor returned the
    raw array for Types not defined by Content Blocks. Check your templates
    whether they can be simplified now.

Publish Assets Command
----------------------

The command :bash:`content-blocks:assets:publish` publishes your public Content
Block `assets` into the Resources/Public folder of the host extension. Normally,
this is performed automatically every time Content Blocks is compiled. In some
deployment scenarios this command could be performed in the CI pipeline to
publish assets without the requirement for a database connection.
