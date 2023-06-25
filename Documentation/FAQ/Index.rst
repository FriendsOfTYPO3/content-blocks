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

Can I use common translations from my e.g. sitepackage or other extensions?
===========================================================================

Yes you can, but we do not recommend it. The reason is that when you reuse your Content Block
in another project, you might not have the same translations available.

To use a translation e.g. from your sitepackage extension, you can do the following as usual:

.. code-block:: html

    <f:translate key="myKey" extensionName="my_sitepackage" />


Can I use a partial from my e.g. sitepackage or other extensions?
=================================================================

Yes you can, but we do not recommend it. The reason is that when you reuse your Content Block
in another project, you might not have the same partials available.

To use a partial e.g. from your sitepackage extension, you have to add the partials root path
via TypoScript:

.. code-block:: typoscript

    tt_content.vendor_package {
        view {
            partialRootPaths {
                20 = EXT:my_sitepackage/Resources/Private/Partials/ContentElements
            }
        }
    }


Can I use a script from my e.g. sitepackage or other extensions?
================================================================

Yes you can, but we do not recommend it. The reason is that when you reuse your Content Block
in another project, you might not have the same scripts available.

To use a script e.g. from your sitepackage extension, you can use the AssetCollector as usual:

.. code-block:: html

    <f:asset.script identifier="jQuery" src="EXT:my_sitepackage/Resources/Public/JavaScript/Libs/jQuery.min.js" />
