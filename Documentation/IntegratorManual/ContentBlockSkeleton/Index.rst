.. include:: /Includes.rst.txt
.. _cb_skeleton:

===============================
Create a Content Block skeleton
===============================

Create a Content Block skeleton in your terminal
================================================

This command creates a bare-minimum content block for your specified
:bash:`content-type` (one of :bash:`content-element`,
:bash:`page-type` or :bash:`record-type`). Required options are
:bash:`vendor`, :bash:`name` and :bash:`extension`. Optionally, you can
define a custom type identifier by providing the :bash:`type` option. This
option is required and has to be an integer value, if you chose the
:bash:`page-type` content type.

This will give you an overview of all available options:

.. code-block:: bash

   vendor/bin/typo3 make:content-block --help

Example creating a Content Block skeleton in one line:

.. code-block:: bash

   vendor/bin/typo3 make:content-block --content-type="content-element" --vendor="my-vendor" --name="my-name" --extension="my_sitepackage"

Alternatively, the command can guide you through the creation by omitting the
required options:

.. code-block:: bash

   vendor/bin/typo3 make:content-block

On non-composer installations use:

.. code-block:: bash

   typo3/sysext/core/bin/typo3 make:content-block

Example interaction:

.. code-block:: bash

   Choose the content type of your content block [Content Element]:
   [content-element] Content Element
   [page-type      ] Page Type
   [record-type    ] Record Type
   > content-element

   Enter your vendor name:
   > my-vendor

   Enter your content block name:
   > my-content-block-name

   Choose an extension in which the content block should be stored:
   [sitepackage] Test Package for content blocks
   > sitepackage
