.. include:: /Includes.rst.txt
.. _cb_guides_page_types:

==========
Page Types
==========

Page Types are a special Content Type in TYPO3. A minimal Page Type looks like
this:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/PageType/blog/EditorInterface.yaml

    name: example/blog
    typeName: 1701284006
    fields:
      - identifier: additional_field
        type: Text

This will create a new Page Type entry above the page tree, which you can drag
and drop as usual. Your custom fields will be added after the `nav_title` field.
SEO fields will be automatically added, if you have the SEO system extension
installed.

Use as Frontend template
========================

Unlike for Content Elements, there is no default rendering definition for Page
Types. Thus, you can't simply define a Frontend.html file. In order to make use
of custom tailored Page Types, you need to connect them with your TypoScript
:typoscript:`PAGE` object. We take the example Blog type from above and add a
:typoscript:`CASE` cObject with the :typoscript:`key` set to
:typoscript:`doktype`. This allows us to render a different frontend template
depending on the Page Type. This setup expects a Blog.html file inside the
Resources/Private/Templates folder in your extension.

.. code-block:: typoscript

    page = PAGE
    page {
      10 = FLUIDTEMPLATE
      10 {
        templateRootPaths {
          0 = EXT:site_package/Resources/Private/Templates/
        }
        templateName = TEXT
        templateName {
          cObject = CASE
          cObject {
            key.field = doktype

            default = TEXT
            default.value = Default

            1701284006 = TEXT
            1701284006.value = Blog
          }
        }
      }
    }

.. hint::

   Many resources in the wild suggest to render page templates depending on the
   selected backend layout. The reason could be that defining new Page Types is
   a more difficult task than defining a new backend layout. However, linking
   a backend layout to the frontend template is a conceptual mistake. As the
   name **backend** layout suggests, this is merely a representation in the
   backend and should not be abused as the frontend representation.

Processing of page data
=======================

In order to have resolved relations also for Page Types, you need to add the
ContentBlocksDataProcessor to your data processor list.

.. code-block:: typoscript

    page {
        10 = FLUIDTEMPLATE
        10 {
            dataProcessing {
                1 = content-blocks
            }
        }
    }
