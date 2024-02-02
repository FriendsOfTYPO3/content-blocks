.. include:: /Includes.rst.txt
.. _cb_definition_language:

========
Language
========

This is the folder for your translations. In fact, if you only have one
language, there is no actual need to maintain translations here. However, it is
best practice to separate labels and configuration.

Labels.xlf
==========

This XLF file is the **english** basis for your translations. All translations
for backend labels as well as for frontend labels are defined here. Translations
to other languages are defined in separate files prefixed with the language code
e.g. **de.Labels.xlf**.

*  Learn more about the :ref:`XLIFF Format in TYPO3 <t3coreapi:xliff>`

Convention
----------

The translation keys follow a **convention** and are registered automatically.
First of all, **title** and **description** are used in various areas for the
Content Type. Field labels consist of the :yaml:`identifier` and **label**
separated by a dot. Same goes for the optional **description**.

:ref:`Collections <field_type_collection>` introduce another nesting level. To
translate their fields, the identifiers are simply separated by a dot.

:ref:`Palettes <field_type_palette>` and :ref:`Tabs <field_type_tab>` have a
special convention as well.

It is also possible to translate the :yaml:`items` option of
:ref:`Select <field_type_select>`, :ref:`Radio <field_type_radio>`
and :ref:`Checkbox <field_type_checkbox>` fields.

Have a look at the example beneath for better understanding.

.. tip::

   You don't have to remember all these rules. The command
   :ref:`content-blocks:language:generate <command_language_generate>` creates
   the Labels.xlf file with all available keys for you.

.. note::

   Labels defined in Labels.xlf will always override :yaml:`label` defined in
   EditorInterface.yaml.

Workflow
--------

The recommended workflow is defining the :yaml:`label` in the
EditorInterface.yaml first. When you are done, you run the command to
auto-generate the Labels.xlf file. After that you can remove the labels from
the yaml definition. You can also skip the first step and generate the xlf
without defining labels first. This will add the :yaml:`identifier` as label
and you can adjust it afterwards. Either way, it is recommended to maintain a
Labels.xlf file so you don't mix labels with configuration.

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
