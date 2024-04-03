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

When to use Page Types
======================

Page Types are best suited for cases where you have a set of pages with common
properties like a teaser text, image or date. The best example is a news article
page. These most often have required fields which are always displayed at a
fixed position, like an author at the bottom of the page. These fields should
be included as page properties rather than specifically defined Content
Elements. The article itself however, should be composed of various Content
Elements. This approach also opens up the possibility to use your Page Types in
plugins. See the `blog extension <https://extensions.typo3.org/extension/blog>`__
which utilises this concept.

Use as Frontend template
========================

Historically, the backend layout / page layout is used as the switch for
frontend templates and is still considered best practice. This means you have
to define an additional page layout for each new Page Type and assign it.

An alternative, more modern approach is to map the frontend template to the
Page Type directly. This makes it possible to have different backend layouts
per Page Type, but still render the same template. This can heavily reduce the
amount of frontend templates which need to be created for each slight layout
variation.

In order to make use of this technique, you need to add a :typoscript:`CASE`
cObject with the :typoscript:`key` set to :typoscript:`doktype`. This allows us
to render a different frontend template depending on the Page Type. This setup
expects a Blog.html file inside the Resources/Private/Templates folder in your
extension.

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

   At the time of writing there is no Core solution to have a page layout as
   default value for a specific Page Type. This has to be done via DataHandler
   hooks. Have a look at `this extension <https://github.com/b13/doktypemapper>`__
   for this.

Backend preview
===============

Just like for Content Elements, you can define an **EditorPreview.html** file
to create a preview of your Page Type. This can be used to preview custom
properties and to link directly to them. To make them prettier it is advised to
utilise CSS bootstrap classes like `card`.

.. code-block:: html

    <div class="card card-size-medium">
        <div class="card-body">
            <be:link.editRecord uid="{data.uid}" table="{data.tableName}" fields="author">
                Author: {data.author}
            </be:link.editRecord>
        </div>
    </div>

Processing of page data
=======================

In order to have resolved relations also for Page Types, you need to add the
:php:`ContentBlocksDataProcessor` to your data processor list. Right now, this
does not resolve relations for the native, standard Page Type.

.. code-block:: typoscript

    page {
        10 = FLUIDTEMPLATE
        10 {
            dataProcessing {
                1 = content-blocks
            }
        }
    }
