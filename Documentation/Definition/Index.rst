.. include:: /Includes.rst.txt
.. _cb_definition:

==========
Definition
==========

The minimal viable definition consists of a folder with a YAML file named
**EditorInterface.yaml** inside. All other resources are split into two folders
named **Assets** and **Source**. These include public resources, translations
and templates.

.. code-block:: none
   :caption: Directory structure of a Content Block

   ├── Assets
   │   └── Icon.svg
   ├── Source
   │   ├── Language
   │   │   └── Labels.xlf
   │   ├── Partials
   │   │   └── Component.html
   │   ├── EditorPreview.html
   │   └── Frontend.html
   └── EditorInterface.yaml

..  contents::
    :local:

EditorInterface.yaml
====================

The heart of a Content Block is the **EditorInterface.yaml** file. This YAML
file defines both the available fields and the structure:

.. code-block:: yaml
   :caption: EXT:some_extension/ContentBlocks/ContentElements/content-block-name

    name: vendor/content-block-name
    fields:
      - identifier: header
        useExistingField: true
      - identifier: my_text_field
        type: Text
        max: 10

First of all, a :yaml:`name` has to be defined. It must be unique inside your
installation. It consists, similarly to composer package names, of a vendor and
a package part separated by a slash. It is used to prefix new field names, new
tables and record type identifiers.

Inside :yaml:`fields` you define the structure and configuration of the
necessary fields. The :yaml:`identifier` has to be unique per Content Block.

It is possible to reuse existing fields with the flag :yaml:`useExistingField`.
This allows e.g. to use the same field :sql:`header` or :sql:`bodytext` across
multiple Content Blocks with different configuration. Be aware that system
fields shouldn't be reused. A list of sane reusable fields can be referenced in
the documentation. Furthermore, own custom fields can be reused as well.

*  Refer to the :ref:`YAML reference <yaml_reference>` for a complete overview.
*  Learn more about :ref:`reusing fields <cb_reuse_existing_fields>`
*  Learn how to :ref:`extend TCA <cb_extendTca>` of Content Blocks (for advanced users).
*  For more information about the YAML syntax refer to `YAML RFC <https://github.com/yaml/summit.yaml.io/wiki/YAML-RFC-Index>`__

Assets
======

The **Assets** folder contains public resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Public**
folder. In composer-mode this folder will be symlinked and published in the
public **_assets** folder. This is the place where you can put your CSS,
JavaScript or image files inside. In order to include these in your template,
you must use custom :ref:`Content Block ViewHelpers <asset_view_helpers>`.

Icon.svg
--------

This is the icon for the Content Type. There is a fallback to a default icon,
but it is recommended to replace it with your own, custom icon. You can find
many official TYPO3 icons `here <https://typo3.github.io/TYPO3.Icons/icons/content.html>`__.
Allowed file extensions are **svg**, **png** and **gif** (in preferred order).

Source
======

The **Source** folder contains private resources. If you are familiar with the
directory structure of extensions, this would be the **Resources/Private**
folder. There is a limited set of directories and files, which you can place
here.

Language
--------

This is the folder for your translations. In fact, if you only have one
language, there is no actual need to maintain translations here. However, it is
best practice to separate labels and configuration.

Labels.xlf
++++++++++

This XLF file is the **english** basis for your translations. All translations
for backend labels as well as for frontend labels are defined here. Translations
to other languages are defined in separate files prefixed with the language code
e.g. **de.Labels.xlf**.

*  Learn more about the :ref:`XLIFF Format in TYPO3 <t3coreapi:xliff>`

The translation keys follow a **convention** and are registered automatically.
First of all, **title** and **description** are used in various areas for the
Content Type. Field labels consist of the :yaml:`identifier` and **label**
separated by a dot. Same goes for the optional **description**.

:ref:`Collections <field_type_collection>` introduce another nesting level. To
translate their fields, the identifiers are simply separated by a dot.

:ref:`Palettes <field_type_palette>` and :ref:`Tabs <field_type_tab>` have a
special convention as well.

Have a look at the example beneath for better understanding.

.. tip::

   You don't have to remember all these rules. The command
   :ref:`content-block:language:generate <command_language_generate>` creates
   the Labels.xlf file with all available keys for you.

.. code-block:: xml

    <?xml version="1.0"?>
    <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file datatype="plaintext" original="Labels.xlf" source-language="en" product-name="example">
            <header/>
            <body>
                <!-- Title and description of the Content Type -->
                <trans-unit id="title" resname="title">
                    <source>This is the Content Type backend title</source>
                </trans-unit>
                <trans-unit id="description" resname="description">
                    <source>This is the Content Type backend description</source>
                </trans-unit>
                <!-- Field labels and descriptions for the backend -->
                <trans-unit id="FIELD_IDENTIFIER.label" resname="FIELD_IDENTIFIER.label">
                    <source>This is the backend label for FIELD_IDENTIFIER</source>
                </trans-unit>
                <trans-unit id="FIELD_IDENTIFIER.description" resname="FIELD_IDENTIFIER.description">
                    <source>This is the backend description for FIELD_IDENTIFIER</source>
                </trans-unit>
                <!-- Collections add another nesting level -->
                <trans-unit id="COLLECTION_IDENTIFIER.FIELD_IDENTIFIER.label" resname="COLLECTION_IDENTIFIER.FIELD_IDENTIFIER.label">
                    <source>This is the backend label for FIELD_IDENTIFIER in Collection COLLECTION_IDENTIFIER</source>
                </trans-unit>
                <!-- Palette labels and descriptions -->
                <trans-unit id="palettes.PALETTE_IDENTIFIER.label">
                    <source>Label for Palette</source>
                </trans-unit>
                <trans-unit id="palettes.PALETTE_IDENTIFIER.description">
                    <source>Description for Palette</source>
                </trans-unit>
                <!-- Palettes inside Collections -->
                <trans-unit id="COLLECTION_IDENTIFIER.palettes.PALETTE_IDENTIFIER.label">
                    <source>Label for Palette in Collection</source>
                </trans-unit>
                <trans-unit id="COLLECTION_IDENTIFIER1.COLLECTION_IDENTIFIER2.palettes.PALETTE_IDENTIFIER.label">
                    <source>Label for Palette in nested Collection</source>
                </trans-unit>
                <!-- Tab labels -->
                <trans-unit id="tabs.TAB_IDENTIFIER">
                    <source>Label for Tab</source>
                </trans-unit>
                <!-- Tab labels inside Collections -->
                <trans-unit id="COLLECTION_IDENTIFIER.tabs.TAB_IDENTIFIER">
                    <source>Label for Tab in Collection</source>
                </trans-unit>
                <trans-unit id="COLLECTION_IDENTIFIER1.COLLECTION_IDENTIFIER2.tabs.TAB_IDENTIFIER">
                    <source>Label for Tab in nested Collection</source>
                </trans-unit>
            </body>
        </file>
    </xliff>

EditorPreview.html
------------------

This file is only available for :ref:`Content Elements <yaml_reference_content_element>`.

The **EditorPreview.html** can be added to customize the backend preview for
your editors. By default, TYPO3 comes with a standard preview renderer. However,
it is specialized in rendering the preview of Core Content Elements. This means
only Core fields like :sql:`header`, :sql:`subheader` or :sql:`bodytext` are
considered. Therefore, it is advised to provide an own preview for custom
Content Elements.

Learn more about :ref:`templating <cb_templating>`.

Frontend.html
-------------

This is the default frontend rendering definition for :ref:`Content Elements <yaml_reference_content_element>`.
You can access your fields by the variable :html:`{data}`.

Learn more about :ref:`templating <cb_templating>`.

Partials
========

For larger Content Elements, you can divide your **Frontend.html** template into
smaller junks by creating separate partials here.

Partials are included as you normally would in any Fluid template.

.. code-block:: html

   <f:render partial="Component.html" arguments="{_all}"/>

*  Learn how to :ref:`share Partials <cb_extension_partials>` between Content Blocks.
