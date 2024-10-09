.. include:: /Includes.rst.txt
.. _cb_guides_page_types:

==========
Page Types
==========

This is a guide. For YAML reference refer to
:ref:`this page <yaml_reference_page_types>`.

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
