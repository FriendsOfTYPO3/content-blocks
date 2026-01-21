..  include:: /Includes.rst.txt
..  _api_backend_preview:

===============
Backend Preview
===============

Backend previews allow you to provide a preview for your frontend in the
backend. Previews are a very important part when creating custom content types
for your editors. They are the only way to help them navigate through the
jungle fo elements in the page module.

A preview can be defined by creating a file `templates/backend-preview.fluid.html`
inside your Content Block. This file is already created for you, if you use the
kickstart command.

..  _api_backend_preview_content_elements:

Content Elements
================

By default, TYPO3 comes with a standard preview renderer for Content Elements.
However, it is specialized in rendering the preview of Core Content Elements.
This means only Core fields like :sql:`header`, :sql:`subheader` or
:sql:`bodytext` are considered. Therefore, it is advised to provide an own
preview for custom Content Elements.

..  versionchanged:: 1.1
    Previews for Content Elements now must define the layout :html:`Preview` and
    any of the sections :html:`Header`, :html:`Content` or :html:`Footer`.

..  code-block:: html
    :caption: EXT:my_package/ContentBlocks/ContentElements/my-element/templates/backend-preview.fluid.html

    <f:layout name="Preview"/>

    <f:section name="Header">
        <div>My header</div>
    </f:section>

    <f:section name="Content">
        <f:asset.css identifier="my-backend-styles" href="{cb:assetPath()}/preview.css"/>
        <div>My content</div>
    </f:section>

    <f:section name="Footer">
        <div>My footer</div>
    </f:section>

A Content Element preview consists of three parts: The header, the content area
and the footer. By defining the appropriate section it is possible to override
the standard preview rendering of TYPO3. If a section is omitted, the fallback
rendering from TYPO3 will be used instead. You can also include CSS just like in
the frontend with :html:`f:asset.css` View Helper.

..  _api_backend_preview_page_types:

Page Types
==========

Previews for :ref:`Page Types <api_page_types>` are displayed at the top of
the content area and beneath the page title. Unlike Content Elements, you don't
have to define any sections.

..  figure:: /API/_Images/page-type-preview.jpg
    :alt: Page Type preview in the TYPO3 backend

This is an example of a Page Type preview.

..  _api_backend_preview_record_types:

Record Types
============

Previews for :ref:`Record Types <api_record_types>` can only be shown as :ref:`nested
child records <cb_nested_elements_backend>` of Content Elements in the Page
Module like so:

.. code-block:: html

    <f:comment>Provide the identifier of the child Collection to render a grid preview</f:comment>
    <f:render partial="PageLayout/Grid" arguments="{data: data, identifier: 'tabs_item'}"/>

.. note::

   In backend context, all hidden relations like Collections or file references
   are displayed by default. Thus, the integrator should style those hidden
   elements accordingly or simply not render them.

   .. code-block:: html

      <!-- Hidden relations like Collections -->
      <f:for each="{data.relations}" as="item">
          <f:if condition="{item.systemProperties.disabled}"><!-- Style or hide --></f:if>
      </f:for>

      <!-- Hidden file references -->
      <f:for each="{data.images}" as="file">
          <f:if condition="{file.properties.hidden}"><!-- Style or hide --></f:if>
      </f:for>

See also:

*  Learn more about :ref:`templating <cb_templating>`.
*  Learn how to include :ref:`shared partials <editor_preview_partials>`
