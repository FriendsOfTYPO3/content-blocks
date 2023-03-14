.. include:: /Includes.rst.txt
.. _faq:

===
FAQ
===

Overriding of Content Blocks
============================

Because Content Block definition packages within the `ContentBlocks` folder of your extension
are merely an abstraction layer, you can override existing Content Block configurations just
like you would override a third-party-extension (e.g. by adding corresponding TCA/ TypoScript/
TSconfig via a sitepackage extension).

Bundling
========

One Content Block definition package represents exactly one Content Block. Bundles can be
realized as distributions (e.g. like TYPO3 minimal distribution) or within
a bundling extension.

Availability on platforms
=========================

The Content Blocks won't be available in the TYPO3 extension repository (TER) and on packagist.org.
Of course it is technically possible to define a Content Block within a separate composer package
and publish them (e.g. on packagist.org).
