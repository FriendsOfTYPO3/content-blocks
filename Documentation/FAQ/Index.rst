.. include:: /Includes.rst.txt
.. _faq:

===
FAQ
===

Overriding of Content Blocks
============================

Because Content Block packages are merely an abstraction layer, you can override
existing Content Block configurations just like you would override
a third-party-extension (e.g. by adding corresponding TCA/ TypoScript/ TSconfig
via a sitepackage extension).


Bundling
========

One composer package represents exactly one Content Block. Bundles can be
realized as distributions (e.g. like TYPO3 minimal distribution) or within
a bundling extension.

Availability on platforms
=========================

Unlike extensions the Content Blocks won't be available in the TYPO3 extension
repository (TER). The main registry for Content Blocks is packagist.org.
