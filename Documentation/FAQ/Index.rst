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


Can I reuse an existing field / column?
=======================

Yes you can. Currently you can reuse only the standard core fields by using the useExistingField flag.

We highly recommend to use the header field this way, because it is used for the title on different places in the backend.

Please remember that the type of a field is even in this case required.

For example, if you want to use the existing column "bodytext", or "header" or "image" you can do one of the following:

.. code-block:: yaml

    group: common
    fields:
        -
            identifier: header
            type: Text
            useExistingField: true
        -
            identifier: bodytext
            type: Textarea
            useExistingField: true
            properties:
                enableRichtext: true
        -
            identifier: image
            type: File
            useExistingField: true
