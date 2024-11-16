.. include:: /Includes.rst.txt

.. _page-types-template:

============================
Page Types frontend template
============================

Historically, the page layout (also known as backend layout) is used as the
switch for frontend templates and is still considered best practice and was even
added as a TYPO3 Core functionality with the :ref:`PAGEVIEW <t3tsref:cobj-pageview>`
object. This means you have to define an additional page layout for each new
Page Type.

Further interesting read: `https://b13.com/blog/simplify-your-typo3-page-configuration`__

Mapping the Page Type to a template
===================================

An alternative, more modern approach is to map the frontend template to the
Page Type directly. This makes it possible to have different page layouts
per Page Type, but still render the same template. This can heavily reduce the
amount of frontend templates which need to be created for each slight layout
variation.

In order to make use of this technique, you need to add a :typoscript:`CASE`
cObject with the :typoscript:`key` set to :typoscript:`doktype`. This allows us
to render a different frontend template depending on the Page Type. This setup
expects a **Blog.html** file inside the **Resources/Private/Templates** folder
in your extension.

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

.. note::

   It is not possible to use this technique together with :ref:`PAGEVIEW <t3tsref:cobj-pageview>`
   as it inherently depends on the page layout.

.. hint::

   At the time of writing there is no Core solution to have a page layout as
   default value for a specific Page Type. This has to be done via DataHandler
   hooks. Have a look at `this extension <https://github.com/b13/doktypemapper>`__
   for this.
