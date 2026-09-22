.. include:: /Includes.rst.txt
.. _content_blocks_view_helper_link_edit_record:

==================
cb:link.editRecord
==================

.. rst-class:: horizbuttons-attention-m

*  Class: :php:`TYPO3\CMS\ContentBlocks\ViewHelpers\Link\EditRecordViewHelper`

Renders an edit link for a backend record, passing :html:`uid` and
:html:`table` to FormEngine. In contrast to the Core
:html:`be:link.editRecord` ViewHelper, it has support for Page Layout anchor
links by default: after closing the edit dialog, the editor automatically
jumps back to the Content Element they edited.

.. note::

    This ViewHelper is intended to be used inside :file:`backend-preview.fluid.html`
    templates, i.e. in the Page Layout context. For all other backend
    contexts, use the Core :html:`be:link.editRecord` ViewHelper instead.

By default, the ViewHelper renders an :html:`<a>` tag linking to the classic
FormEngine edit view. When :html:`contextual` is set to :html:`true`, it
instead renders a :html:`<typo3-backend-contextual-record-edit-trigger>`
element, which opens the record in the contextual editing overlay directly
in the Page Layout.

Arguments
=========

..  confval-menu::
    :name: confval-link-edit-record-arguments
    :display: table
    :type:
    :default:
    :required:

.. confval:: uid
   :name: link-edit-record-uid
   :required: false
   :type: int

   uid of the record to be edited. Must be a positive integer. Not required
   if :yaml:`record` is given instead.

.. confval:: table
   :name: link-edit-record-table
   :required: false
   :type: string

   Target database table. Not required if :yaml:`record` is given instead.

.. confval:: record
   :name: link-edit-record-record
   :required: false
   :type: object

   A :php:`TYPO3\CMS\Core\Domain\RecordInterface` object, for example
   :html:`{data}` or a Collection item. Can be used instead of :html:`uid`
   and :html:`table`. If given, it takes precedence over :html:`uid` and
   :html:`table`.

.. confval:: fields
   :name: link-edit-record-fields
   :required: false
   :type: string

   Restrict the edit form to only these fields (comma separated list).

.. confval:: module
   :name: link-edit-record-module
   :required: false
   :type: string
   :default: (current module identifier)

   Sets the module identifier for context, marking it as active while
   editing the record. Falls back to the current backend module, if not set.

.. confval:: returnUrl
   :name: link-edit-record-returnUrl
   :required: false
   :type: string
   :default: (current request URL)

   URL to return to after closing the edit dialog. Falls back to the current
   request URL, appended with the anchor :html:`#element-{table}-{uid}`, if
   not set.

.. confval:: contextual
   :name: link-edit-record-contextual
   :required: false
   :type: bool
   :default: false

   Render a contextual edit trigger (opens the contextual editing overlay in
   Page Layout) instead of a classic FormEngine edit link.

Examples
========

Link to the record-edit action
--------------------------------

.. code-block:: html

    <cb:link.editRecord uid="42" table="a_table" returnUrl="foo/bar" />

Output:

.. code-block:: html

    <a href="/typo3/record/edit?edit[a_table][42]=edit&returnUrl=foo/bar">
        Edit record
    </a>

Using the record object
-----------------------

Instead of passing :html:`uid` and :html:`table` separately, the
:html:`{data}` variable (or a Collection item) can be passed directly via
:html:`record`.

.. code-block:: html

    <cb:link.editRecord record="{data}">
        <f:format.raw>{data.bodytext}</f:format.raw>
    </cb:link.editRecord>

Restricting editable fields
---------------------------

Link to edit only the fields :html:`title` and :html:`subtitle` of page
uid=42, and return to :html:`foo/bar` afterwards.

.. note::
   The field identifiers must be the full identifiers with prefixes.

.. code-block:: html

    <cb:link.editRecord uid="42" table="pages" fields="title,subtitle" returnUrl="foo/bar">
        Edit record
    </cb:link.editRecord>

Output:

.. code-block:: html

    <a href="/typo3/record/edit?edit[pages][42]=edit&returnUrl=foo/bar&columnsOnly[pages]=title,subtitle">
        Edit record
    </a>

Returning to a specific backend module
------------------------------------------

Link to edit page uid=3 and return back to the backend module
:html:`web_MyextensionList`.

.. code-block:: html

    <cb:link.editRecord uid="3" table="pages" returnUrl="{f:be.uri(route: 'web_MyextensionList')}">
        Edit record
    </cb:link.editRecord>

Contextual editing overlay
--------------------------

Open the record in the contextual editing overlay instead of the classic
FormEngine view.

.. code-block:: html

    <cb:link.editRecord uid="42" table="pages" fields="title,subtitle" contextual="{true}">
        Edit page properties
    </cb:link.editRecord>

Output:

.. code-block:: html

    <typo3-backend-contextual-record-edit-trigger
        url="/typo3/record/edit/contextual?edit[pages][42]=edit&columnsOnly[pages][0]=title&columnsOnly[pages][1]=subtitle"
        edit-url="/typo3/record/edit?edit[pages][42]=edit&columnsOnly[pages][0]=title&columnsOnly[pages][1]=subtitle"
    >
        Edit page properties
    </typo3-backend-contextual-record-edit-trigger>

Backend preview: Content Element
--------------------------------

A full example inside a :file:`backend-preview.fluid.html` template, linking
the bodytext of a Content Element.

.. code-block:: html

    <html
        xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
        xmlns:cb="http://typo3.org/ns/TYPO3/CMS/ContentBlocks/ViewHelpers"
        data-namespace-typo3-fluid="true"
    >

    <f:layout name="Preview"/>

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

Backend preview: linking Collection items
-----------------------------------------

If you have a custom preview for your Collection, you can link the items
individually, making it easier for editors to edit one specific item
quickly. The important part is to add the :html:`id` attribute following the
schema :html:`element-{table}-{uid}`.

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

Migrating from :html:`be:link.editRecord`
=================================================

If you already used the :html:`be:link.editRecord` ViewHelper, a quick search
and replace is sufficient: :html:`<be:link.editRecord` -> :html:`<cb:link.editRecord`
and :html:`</be:link.editRecord>` -> :html:`</cb:link.editRecord>`. The old
parameters :html:`uid` and :html:`table` still work as before.

See also
========

*  https://docs.typo3.org/permalink/t3viewhelper:typo3-backend-link-editrecord
*  :ref:`Changelog 1.3 <changelog-1.3>`
