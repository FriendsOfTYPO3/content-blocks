.. include:: /Includes.rst.txt
.. _changelog-1.1:

===
1.1
===

Features
========

Backend Preview areas
---------------------

It is now possible to adjust the header and footer for
:ref:`backend previews <api_backend_preview_content_elements>` of
Content Elements:

..  code-block::
    :caption: EXT:my_package/ContentBlocks/ContentElements/my-element/templates/backend-preview.html

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

Deprecations
============

Backend Preview
---------------

Backend previews for Content Elements must use the new layout :html:`Preview`
now. Content Blocks will fall back to the old behavior, if the layout is omitted
and will log a deprecation-level log entry.
