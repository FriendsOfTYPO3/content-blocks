.. include:: /Includes.rst.txt
.. _cb_definition:

========================
Defining a Content Block
========================

Directory structure of a Content Block
======================================

A Content Block definition package has the following files and directory structure:

+-------------------------------------------------+------------+--------------------------------+
| Directory / File                                | Mandatory? | Created via make:content-block |
+-------------------------------------------------+------------+--------------------------------+
| EditorInterface.yaml                            | x          | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Source/Language/Labels.xlf                      | x          | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Source/EditorPreview.html                       |            | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Source/Frontend.html                            |            | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Assets/EditorPreview.css                        |            | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Assets/Frontend.css                             |            | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Assets/Frontend.js                              |            | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Assets/ContentBlockIcon.(svg/png/gif)           |            | x                              |
+-------------------------------------------------+------------+--------------------------------+

Content Block definition package files explained
================================================

EditorInterface.yaml
--------------------

refers to: `YAML RFC <https://github.com/yaml/summit.yaml.io/wiki/YAML-RFC-Index>`__

**You must**

*  provide this file
*  define the editor interface of exactly one Content Block
*  define the unique name of the Content Block, all the fields and their position in the editing interface

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


Source/Language/Labels.xlf
-------------------------------------

**You may**

*  provide that file
*  define your labels with the XLF links in the configuration file

Labels for the editing interface, as well as frontend labels, are stored in the
`Source/Language/Labels.xlf`(translated files will be e.g. `de.Labels.xlf`).

It is recommended to apply the :ref:`coding guidelines for labels <t3coreapi:xliff>`
to your Content Blocks as well. E.g. for backend labels that would be:
`<code-block-identifier>.<field-identifier>.title`

Or the description in the backend, e.g. the description in newContentElementWizard:
`<code-block-identifier>.<field-identifier>.description`

Example for a label of a field:
`<field-identifier>.label`

Example for a description of a field:
`<field-identifier>.description`

This goes analogously for collection fields, in this case the field identifier
of the collection field is used as a prefix:
`<collection-field-identifier>.<field-identifier>.label`
