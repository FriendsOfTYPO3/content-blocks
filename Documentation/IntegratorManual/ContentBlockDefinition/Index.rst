.. include:: /Includes.rst.txt
.. _cb_definition:

========================
Defining a Content Block
========================

Directory structure of a Content Block
======================================

A Content Block package has the following files and directory structure:

+----------------------------------------+------------+-----------------+
| Directory / File                       | Mandatory? | Created via GUI |
+========================================+============+=================+
| composer.json                          |      x     |         x       |
+----------------------------------------+------------+-----------------+
| ContentBlockIcon.(svg/png/gif)         |      x     |         x       |
+----------------------------------------+------------+-----------------+
| EditorInterface.yaml                   |      x     |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Private/Language/Labels.xlf  |      x     |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Private/EditorPreview.html   |            |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Private/Frontend.html        |            |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Public/EditorPreview.css     |            |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Public/Frontend.css          |            |         x       |
+----------------------------------------+------------+-----------------+
| Resources/Public/Frontend.js           |            |         x       |
+----------------------------------------+------------+-----------------+


Content Block package files explained
=====================================

composer.json
-------------

refers to: `Composer schema <https://getcomposer.org/doc/04-schema.md>`__

The Content Block ID (CType) derives from the package name. Therefore one
composer package represents exactly one Content Block.

**You must**

*  provide this file
*  set the type property to: `typo3-content-block`

**You may**

*  use the full composer.json config and define autoloading for ViewHelpers etc.


EditorInterface.yaml
--------------------

refers to: `YAML RFC <https://github.com/yaml/summit.yaml.io/wiki/YAML-RFC-Index>`__

**You must**

*  provide this file
*  define the editor interface of exactly one Content Block
*  define all the fields and their position in the editing interface

See :ref:`yaml_reference`.

A field is localize-able by default. Setting the localization explicitly is
only necessary, if a special localization method is required.


ContentBlockIcon.(svg|png|gif)
------------------------------

This is the icon for the Content Block. There is no fallback by intention, but
it is easy to generate an SVG with the Content Block name as a graphical
representation.

**You must**

*  provide this file
*  provide that file in the format svg or png or gif
*  provide a file with 1:1 dimensions


Resources/Private/Language/Labels.xlf
-------------------------------------

**You may**

*  provide that file
*  define your labels with the XLF links in the configuration file

Labels for the editing interface, as well as frontend labels, are stored in the
`Resources/Private/Language/Labels.xlf`(translated files will be e.g. `de.Labels.xlf`).

It is recommended to apply the :ref:`coding guidelines for labels <t3coreapi:xliff>`
to your Content Blocks as well. E.g. for backend labels that would be:
`<code-block-identifier>.<field-identifier>`.
