.. include:: /Includes.rst.txt
.. _faq:

===
FAQ
===

Overriding of Content Blocks
============================

@todo Right now overriding Content Blocks is not officially supported. There
is one :ref:`Event <cb_extendTca>`, which you can use to override the generated
TCA. This Event, however, might disappear in the future in favor of a real
override API. Overriding TypoScript, TsConfig or UserTsConfig should work as
well.

Bundling
========

One or more Content Blocks can be hosted by one extension. You can provide one
extension, which holds all Content Blocks you need or split them into multiple
extensions.

Can I use common translations from my e.g. sitepackage or other extensions?
===========================================================================

Yes you can, but it is not recommend. The reason is that when you reuse your
Content Block in another project, you might not have the same translations
available.

To use a translation e.g. from your sitepackage extension, you can do the following as usual:

.. code-block:: html

    <f:translate key="myKey" extensionName="my_sitepackage" />


Can I use a partial from my e.g. sitepackage or other extensions?
=================================================================

Yes you can, but it is not recommend. The reason is that when you reuse your
Content Block in another project, you might not have the same partials available.

To use a partial e.g. from your sitepackage extension, you have to add the
partials root path via TypoScript:

.. code-block:: typoscript

    tt_content.vendor_name {
        view {
            partialRootPaths {
                20 = EXT:my_sitepackage/Resources/Private/Partials/ContentElements
            }
        }
    }


Can I use a script from my e.g. sitepackage or other extensions?
================================================================

Yes you can, but it is not recommend. The reason is that when you reuse your
Content Block in another project, you might not have the same scripts available.

To use a script e.g. from your sitepackage extension, you can use the AssetCollector as usual:

.. code-block:: html

    <f:asset.script identifier="jQuery" src="EXT:my_sitepackage/Resources/Public/JavaScript/Libs/jQuery.min.js" />
