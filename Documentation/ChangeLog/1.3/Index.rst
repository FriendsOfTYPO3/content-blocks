.. include:: /Includes.rst.txt
.. _changelog-1.3:

===
1.3
===

..  contents::

Features
========

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
