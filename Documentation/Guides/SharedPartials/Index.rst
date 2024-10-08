.. include:: /Includes.rst.txt
.. _cb_extension_partials:

===============
Shared Partials
===============

Sometimes you want to reuse Partials from your extension in your Content Block
template. This is done by :ref:`extending <cb_extendTyposcript>` the :typoscript:`partialRootPaths` of the
default Content Block :ref:`FLUIDTEMPLATE <t3tsref:cobj-fluidtemplate>`
definition.

For every Content Block
========================

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    lib.contentBlock {
        partialRootPaths {
            100 = EXT:site_package/Resources/Private/Partials/ContentElements/
        }
    }

For a specific Content Block
============================

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/TypoScript/setup.typoscript

    tt_content.myvendor_mycontentblockname {
        partialRootPaths {
            100 = EXT:my_sitepackage/Resources/Private/Partials/ContentElements/
        }
    }

.. note::

   Content Blocks reserves indexes below `100`.

.. _editor_preview_partials:

For all backend-preview.html templates
======================================

Sometimes it is needed to include partials from another source to be used in the
preview. For this some Page TsConfig is needed. This can be included in the
**page.tsconfig** file inside your extension, which is automatically loaded. It
is also possible to provide additional layout root paths.

.. code-block:: typoscript
   :caption: EXT:my_extension/Configuration/page.tsconfig

    tx_content_blocks {
      view {
        layoutRootPaths {
          10 = EXT:my_extension/Resources/Private/Layouts/
        }
        partialRootPaths {
          10 = EXT:my_extension/Resources/Private/Partials/
        }
      }
    }
