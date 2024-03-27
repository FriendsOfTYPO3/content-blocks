.. include:: /Includes.rst.txt
.. _cb_nestedContentElements:

=======================
Nested Content Elements
=======================

It is possible to nest Content Elements within Content Blocks.
By default, TYPO3 would render those nested elements within the TYPO3 Page
Module in the backend and the frontend output. Content Blocks delivers an API
and integration for common setups to prevent this unwanted behaviour.

.. note::

   This will not replace proper grid extensions like EXT:container, as this
   solution does not provide drag and drop or creation of new child elements
   in the Page Module.

How to create nested Content Elements
=====================================

In order to have nested Content Elements, we need to make use of the field type
:ref:`Collection <field_type_collection>`. This field type allows us to have a
relation to another table. In our case :sql:`tt_content`.

.. code-block:: yaml
   :caption: EXT:my_extension/ContentBlocks/ContentElements/tabs/EditorInterface.yaml

    name: example/tabs
    fields:
      - identifier: header
        useExistingField: true
      - identifier: tabs_item
        type: Collection
        minitems: 1
        foreign_table: tt_content
        overrideChildTca:
          columns:
            CType:
              config:
                default: example_text

This config creates a field, where you can create new Content Elements within
your root Content Element. For better usability the default CType can be
overridden with :yaml:`overrideChildTca`. Right now, it is not possible to
restrict certain CTypes.

Render nested Content Elements in the frontend
==============================================

There are two ways to render nested Content Elements. The first one is to reuse
the basic rendering definition of the child element. For this it is needed to
start a sub-rendering within the Fluid template by triggering the compilation of
the TypoScript rendering definition for the child element type. Each necessary
information can be retrieved from the Content Block data object.

.. code-block:: html
   :caption: EXT:my_extension/ContentBlocks/ContentElements/tabs/Source/Frontend.html

    <f:for each="{data.tabs_item}" as="item" iteration="i">
        <div class="tab-item">
            <f:cObject typoscriptObjectPath="{item.tableName}.{item.typeName}" table="{item.tableName}" data="{item._raw}"/>
        </div>
    </f:for>

The second method is to define an alternative rendering within your Fluid
template. This means you can have a default rendering definition for your
Content Element, when used as a root Content Element and an alternative one if
used as a child. This method is a lot more flexible, but requires a little bit
more work.

.. code-block:: html
   :caption: EXT:my_extension/ContentBlocks/ContentElements/tabs/Source/Frontend.html

    <f:for each="{data.tabs_item}" as="item" iteration="i">
        <div class="tab-item">
            <h2>{item.header}</h2>
            <f:format.html>{data.bodytext}</f:format.html>
        </div>
    </f:for>

.. tip::

   You can also use :ref:`global partials <cb_extension_partials>` for the
   second method to have less duplication.

Render nested Content Elements in the backend
=============================================

Similarly to frontend rendering, it's also possible to render nested content in
the backend. For this Content Blocks provides ready to use Fluid partials which
are able to render backend previews the same way the Core page layout does it.

.. code-block:: html
   :caption: EXT:my_extension/ContentBlocks/ContentElements/tabs/Source/BackendPreview.html

    <f:render partial="PageLayout/Grid" arguments="{data: data, identifier: 'tabs_item'}"/>

The partial is called **PageLayout/Grid** and accepts your current Content Block
data object as well as the identifier of the Collection field, which you want
to render.

.. figure:: ./NestedContentPreview.png

   The partial renders the grid layout from the Core.

This preview is limited to control buttons like edit, delete and hide. No
support for drag and drop or creation of new child elements is given.

When to use nested Content Elements vs. container extensions
============================================================

Many problems can be solved with either solution. However, as a rule of thumb,
as soon as you need dynamic multi-grid components for your page, you are better
off using something like EXT:container. For example "Two-Column container",
"1-1-2 Container" or "Wrapper Container".

On the other hand, if you create a self-contained element, which should hold
child Content Element relations and these child elements are unlikely to be
moved elsewhere, you might be better off using simple nested Content Elements.
An example could be a "Tab module" or a "Content Carousel".

Concept
=======

The nesting is done via one :confval:`database field <collection_foreign_field>`
holding a reference to the parent Content Element. The :sql:`colPos` column will
always be `0`, which is also the reason why it would otherwise be rendered by
TYPO3 even though created within another Content Element.

Extensions like EXT:container work completely different as they assign a
specific colPos value to the child elements.

Preventing output in frontend
=============================

Output in frontend is prevented by extending :typoscript:`styles.content.get.where`
condition. This is done via :typoscript:`postUserFunc`, which will extract all
defined parent reference columns. Those are added to the SQL statement in order
to prevent fetching any child elements.

.. note::

   If you do not build upon :typoscript:`styles.content.get.where`, you need to
   integrate the logic yourself. The necessary API providing all the columns is
   available via :php:`TYPO3\CMS\ContentBlocks\Service\ContentElementParentFieldService->getAllFieldNames()`.
   This can be used to apply the same approach to :php:`TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor`.

Preventing output in backend
============================

TYPO3 provides an event to alter the SQL query to fetch content for the Page
Module in the backend. Content Blocks adds an event listener to apply the same
logic as for the frontend.
