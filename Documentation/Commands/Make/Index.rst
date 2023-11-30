.. include:: /Includes.rst.txt
.. _cb_skeleton:

=================
Kickstart command
=================

The command :bash:`make:content-block` creates a bare-minimum Content Block.
This is actually an alias for :bash:`content-blocks:create`, which is inspired
by the `EXT:make` extension.

Options
=======

.. confval:: content-type

   :Required: true
   :Type: string

   :bash:`content-element`, :bash:`page-type` or :bash:`record-type`

.. confval:: vendor

   :Required: true
   :Type: string

   Your vendor name. Lowercase, separated by dashes.

.. confval:: name

   :Required: true
   :Type: string

   Your Content Block name (this is not the title). Lowercase, separated by dashes.

.. confval:: extension

   :Required: true
   :Type: string

   The host extension, where to store your new Content Block.

.. confval:: type-name

   :Required: false
   :Type: string|int

   Custom type name. Required for content-type :bash:`page-type` (must be int).

.. important::

    The :bash:`type-name` option is required and has to be an integer value, if you
    choose the :bash:`page-type` content type.

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


After running the make command
==============================

In order to create the newly added database tables or fields, first you have to
clear the caches and then run the database compare. You can do the same in the
TYPO3 Backend by using the Database Analyzer. Repeat this step every time you
add new fields to your Content Block definition.

.. code-block:: bash

   vendor/bin/typo3 cache:flush -g system
   vendor/bin/typo3 extension:setup --extension=my_sitepackage
