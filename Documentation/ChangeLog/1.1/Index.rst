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

..  code-block:: html
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

Skeletons for creation of new Content Blocks
--------------------------------------------

It is now possible to define an additional :shell:`skeleton-path` for the
:shell:`make:content-block` command. This is a path relative to your current
working directory, which contains a skeleton for one or more content types. If
no path is defined, the command will fall back to `content-blocks-skeleton`.

..  code-block:: shell

    vendor/bin/typo3 make:content-block --skeleton-path="my-skeleton"

..  code-block::
    :caption: A folder "my-skeleton" has a skeleton for different content types

    my-skeleton
    ├── content-element
    │   ├─ assets
    │   │  └─ icon.svg
    │   └─ templates
    │      ├─ backend-preview.html
    │      └─ frontend.html
    ├── page-type
    └── record-type

Deprecations
============

Backend Preview
---------------

Backend previews for Content Elements must use the new layout :html:`Preview`
now. Content Blocks will fall back to the old behavior, if the layout is omitted
and will log a deprecation-level log entry.

Before:

..  code-block:: html

    <f:asset.css identifier="my-backend-styles" href="{cb:assetPath()}/preview.css"/>
    <div>My content</div>

After:

..  code-block:: html

    <f:layout name="Preview"/>

    <f:section name="Content">
        <f:asset.css identifier="my-backend-styles" href="{cb:assetPath()}/preview.css"/>
        <div>My content</div>
    </f:section>
