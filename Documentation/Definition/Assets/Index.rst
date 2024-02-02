.. include:: /Includes.rst.txt
.. _cb_definition_assets:

======
Assets
======

The **Assets** folder contains public resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Public**
folder. In composer-mode this folder will be symlinked and published in the
public **_assets** folder. This is the place where you can put your CSS,
JavaScript or image files inside. In order to include these in your template,
you must use custom :ref:`Content Block ViewHelpers <asset_view_helpers>`.

Icon.svg
========

This is the icon for the Content Type. There is a fallback to a default icon,
but it is **recommended** to replace it with your own, custom icon. You can find
many official TYPO3 icons `here <https://typo3.github.io/TYPO3.Icons/icons/content.html>`__.
Allowed file extensions are **svg**, **png** and **gif** (in preferred order).
