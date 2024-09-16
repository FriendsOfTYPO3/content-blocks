.. include:: /Includes.rst.txt
.. _cb_definition_assets:

======
assets
======

The **assets** folder contains public resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Public**
folder. This is the place where you can put your CSS, JavaScript or image files
inside. In order to include these in your template, you must use custom
:ref:`Content Block ViewHelpers <asset_view_helpers>`.

This folder will be symlinked or copied to
**Resources/Public/ContentBlocks/<vendor>/<name>** of the host extension. This
is to keep the modular style of a Content Type but still leverage the TYPO3
asset publishing mechanism.

When using a version control system, that path should be ignored by adding this
line to the host extension's :file:`.gitignore` file:

.. code-block::
    /Resources/Public/ContentBlocks

icon.svg
========

This is the icon for the Content Type. There is a fallback to a default icon,
but it is **recommended** to replace it with your own, custom icon. You can find
many official TYPO3 icons `here <https://typo3.github.io/TYPO3.Icons/icons/content.html>`__.
Allowed file extensions are **svg**, **png** and **gif** (in preferred order).

icon-hide-in-menu.svg
==================

This is a special icon for Page Types for the "hide in menu" state. The same
logic applies as for the standard icon.svg.

IconRoot.svg
============

This is a special icon for Page Types for the "is_siteroot" state. The same
logic applies as for the standard icon.svg.
