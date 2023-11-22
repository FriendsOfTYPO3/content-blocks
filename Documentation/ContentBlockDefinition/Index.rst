.. include:: /Includes.rst.txt
.. _cb_definition:

========================
Defining a Content Block
========================

Directory structure of a Content Block
======================================

A Content Block definition has the following files and directory structure:

+-------------------------------------------------+------------+--------------------------------+
| Directory / File                                | Mandatory? | Created via make:content-block |
+-------------------------------------------------+------------+--------------------------------+
| EditorInterface.yaml                            | x          | x                              |
+-------------------------------------------------+------------+--------------------------------+
| Source/Language/Labels.xlf                      | (*)        | x                              |
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
| Assets/Icon.(svg/png/gif)                       | (*)        | x                              |
+-------------------------------------------------+------------+--------------------------------+

(*) highly recommended

Content Block definition files explained
========================================

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

Assets/Icon.(svg|png|gif)
-------------------------

This is the icon for the Content Block. There is a fallback to a default icon,
but it is recommended to replace it with your own, custom icon. You can find
many official TYPO3 icons `here <https://typo3.github.io/TYPO3.Icons/icons/content.html>`__.

**You should**

*  provide this file
*  provide that file in the format svg or png or gif
*  provide a file with 1:1 dimensions

Source/Language/Labels.xlf
--------------------------

**You may**

*  provide that file
*  define your labels with the XLF paths in the configuration file

Labels for the editing interface, as well as frontend labels, are stored in the
`Source/Language/Labels.xlf` (translated files will be e.g. `de.Labels.xlf`).

It is recommended to apply the :ref:`coding guidelines for the XLIFF Format <t3coreapi:xliff>`.

Labels and descriptions for the backend preview and the editing interface will
be automatically registered by a convention. See the following examples on how
this works:

.. code-block:: xml

    <?xml version="1.0"?>
    <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file datatype="plaintext" original="Labels.xlf" source-language="en" product-name="example">
            <header/>
            <body>
                <trans-unit id="title" resname="title">
                    <source>This is the backend title</source>
                </trans-unit>
                <trans-unit id="description" resname="description">
                    <source>This is the backend description</source>
                </trans-unit>
                <trans-unit id="FIELD_IDENTIFIER.label" resname="FIELD_IDENTIFIER.label">
                    <source>This is the backend label for FIELD_IDENTIFIER</source>
                </trans-unit>
                <trans-unit id="COLLECTION_IDENTIFIER.FIELD_IDENTIFIER.label" resname="COLLECTION_IDENTIFIER.FIELD_IDENTIFIER.label">
                    <source>This is the backend label for FIELD_IDENTIFIER in Collection COLLECTION_IDENTIFIER</source>
                </trans-unit>
            </body>
        </file>
    </xliff>

There are more conventions for special field types like
:ref:`Palettes <field_type_palette>` and :ref:`Tabs <field_type_tab>`. See the
respective documentation for more insights.
