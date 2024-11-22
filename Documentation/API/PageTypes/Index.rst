.. include:: /Includes.rst.txt
.. _api_page_types:
.. _cb_guides_page_types:

==========
Page Types
==========

For YAML reference refer to :ref:`this page <yaml_reference_page_types>`.

Page Types (also known as doktypes) do exist since the beginning of time.
However, it was always hard to create custom ones and there were no extensions
to help with creating them as they exist for Content Elements. Due to this
circumstances you might not be familiar with the concept of custom Page Types.
Basically, you can create a variation which is different from the standard page
type and add special properties to it. These can be used in templates or even
custom plugins. This opens up many possibilities and can enhance user experience
in the backend.

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

Frontend template
=================

There is no default frontend template for Page Types, as this depends heavily
on your setup. Typically the sitepackage extension of your site defines the
page templates depending on the selected page layout.

Read more about :ref:`Page Type / template mapping <page-types-template>`.

Backend preview
===============

Just like for Content Elements, you can define an **backend-preview.html** file
to create a preview of your Page Type. This can be used to preview custom
properties and to link directly to them. To make them prettier it is advised to
utilise CSS bootstrap classes like `card`.

.. code-block:: html

    <div class="card card-size-medium">
        <div class="card-body">
            <be:link.editRecord uid="{data.uid}" table="{data.mainType}" fields="author">
                Author: {data.author}
            </be:link.editRecord>
        </div>
    </div>

Icons for states
================

Page Types can have special states like disabled or hidden in menu. Depending
on this state, the icon changes or is overlaid with another icon. As for now,
the "hide in menu" and "is site root" states can be supplied via custom icons.
Put an icon with the name **icon-hide-in-menu.svg** and one with
**icon-root.svg** inside your assets folder to use them.

.. code-block:: none
   :caption: Directory structure of a Content Block

   ├── assets
   │   │── icon.svg
   │   │── icon-hide-in-menu.svg
   │   └── icon-root.svg
   └── config.yaml

Processing of page data
=======================

In order to have resolved relations also for Page Types, you need to add the
:php:`ContentBlocksDataProcessor` to your data processor list.

.. code-block:: typoscript

    page {
        10 = FLUIDTEMPLATE
        10 {
            dataProcessing {
                1 = content-blocks
            }
        }
    }

Remove entry in Page Tree `NewPageDragArea`
===========================================

In some cases you don't want your page type to be selectable in the drag area
of the page tree. You can remove it with user tsconfig.

.. code-block:: typoscript
   :caption: EXT:site_package/Configuration/user.tsconfig

    options {
      pageTree {
        doktypesToShowInNewPageDragArea := removeFromList(1701284006)
      }
    }
