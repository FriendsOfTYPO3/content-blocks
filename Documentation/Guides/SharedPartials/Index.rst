.. include:: /Includes.rst.txt
.. _cb_extension_partials:

===============
Shared Partials
===============

Sometimes you want to reuse Partials from your extension in your Content Block
template. This is done by extending the :typoscript:`partialRootPaths` of the
default Content Block :ref:`FLUIDTEMPLATE <t3tsref:cobj-fluidtemplate>`
definition.

Make Partials available for every Content Block
===============================================

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    lib.contentBlock {
        partialRootPaths {
            100 = EXT:site_package/Resources/Private/Partials/ContentElements/
        }
    }

Make Partials available for a specific Content Block
====================================================

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    tt_content.myvendor_mycontentblockname {
        partialRootPaths {
            100 = EXT:my_sitepackage/Resources/Private/Partials/ContentElements/
        }
    }

.. note::

   Content Blocks reserves indexes below `100`.
