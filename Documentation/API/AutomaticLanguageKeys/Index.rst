..  include:: /Includes.rst.txt
..  _api_automatic_language_keys:

=======================
Language Key Convention
=======================

This feature refers to the :ref:`labels.xlf <cb_definition_language>` file.

The translation keys follow a **convention** and are registered automatically.
This feature is available for the following options (more may come).

*  :ref:`element title <confval-root-title>`
*  :ref:`element description <confval-content-element-description>`
*  :ref:`Field label <confval-field-types-label>`
*  :ref:`Field description <confval-field-types-description>`
*  :ref:`Palette label <field_type_palette>`
*  :ref:`Palette description <field_type_palette>`
*  :ref:`Tab <field_type_tab>`
*  :ref:`FlexForm Sheets <field_type_flexform_section>`
*  :ref:`FlexForm Container <confval-section-container>`

It is also possible to translate the :yaml:`items` option of
:ref:`Select <field_type_select>`, :ref:`Radio <field_type_radio>`
and :ref:`Checkbox <field_type_checkbox>` fields.

The convention is best explained by examples. Have a look at the example
beneath.

.. note::

   Labels defined in labels.xlf will always override :yaml:`label` defined in
   config.yaml.

Workflow
========

The recommended workflow is defining the :yaml:`label` in the config.yaml first.
When you are done, you run the :ref:`command <command_language_generate>` to
auto-generate the labels.xlf file. After that you can remove the labels from the
yaml definition. You can also skip the first step and generate the xlf without
defining labels first. This will add the :yaml:`identifier` as label and you can
adjust it afterwards. Either way, it is recommended to maintain a labels.xlf
file so you don't mix labels with configuration.

Convention schema
=================

The schema below displays most common conventions. Text in uppercase refers to
values defined in :yaml:`identifier`.

.. tip::

   You don't have to remember all these rules. The command
   :ref:`content-blocks:language:generate <command_language_generate>` creates
   the labels.xlf file with all available keys for you.

.. code-block:: xml

    <?xml version="1.0"?>
    <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file datatype="plaintext" original="labels.xlf" source-language="en" product-name="example">
            <header/>
            <body>
                <!-- Title and description of the Content Type -->
                <trans-unit id="title">
                    <source>This is the Content Type backend title</source>
                </trans-unit>
                <trans-unit id="description">
                    <source>This is the Content Type backend description</source>
                </trans-unit>
                <!-- Field labels and descriptions for the backend -->
                <trans-unit id="FIELD_IDENTIFIER.label">
                    <source>This is the backend label for FIELD_IDENTIFIER</source>
                </trans-unit>
                <trans-unit id="FIELD_IDENTIFIER.description">
                    <source>This is the backend description for FIELD_IDENTIFIER</source>
                </trans-unit>
                <!-- Collections add another nesting level -->
                <trans-unit id="COLLECTION_IDENTIFIER.FIELD_IDENTIFIER.label">
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
