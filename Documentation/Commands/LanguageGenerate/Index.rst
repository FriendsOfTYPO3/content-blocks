.. include:: /Includes.rst.txt
.. _command_language_generate:

=========================
Language Generate command
=========================

The command :bash:`content-blocks:language:generate` updates the Labels.xlf
content for the specified Content Block. Already set labels in both the
Labels.xlf file and in the EditorInterface.yaml are considered. The Labels.xlf
file has precedence over inline labels in the YAML definition. Optional keys
like descriptions or labels for existing fields will only be be generated if
they have been set manually. Custom translations, which don't belong to the
automatic language keys, will be kept and appended to the end.

Arguments
=========

.. confval:: content-block

   :Required: true (false if :bash:`--extension` provided)
   :Type: string

   The Content Block to generate the xlf for.

Options
=======

.. confval:: print

   :Shortcut: p
   :Type: bool

   Print Labels.xlf to terminal instead of writing to file system.

.. confval:: extension

   :Shortcut: e
   :Type: string

   Write Labels.xlf to all Content Blocks within the given extension.

Write up-to-date Labels.xlf file for Content Block example/example.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example

Update all Labels.xlf files within the extension "site_package".

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example --extension="site_package"

Print up-to-date Labels.xlf content for Content Block example/example.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example --print

Example output:

.. code-block:: xml

    <?xml version="1.0"?>
    <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file datatype="plaintext" original="Labels.xlf" source-language="en" date="2023-12-03T08:37:53+00:00" product-name="demo/demo">
            <header/>
            <body>
                <trans-unit id="title" resname="title">
                    <source>My demo title</source>
                </trans-unit>
                <trans-unit id="description" resname="description">
                    <source>This is just a demo/demo</source>
                </trans-unit>
                <trans-unit id="header.label" resname="header.label">
                    <source>Existing field override</source>
                </trans-unit>
                <trans-unit id="slug.label" resname="slug.label">
                    <source>My Slug</source>
                </trans-unit>
                <trans-unit id="slug.description" resname="slug.description">
                    <source>My Slug Description</source>
                </trans-unit>
                <trans-unit id="my_collection.label" resname="my_collection.label">
                    <source>my_collection</source>
                </trans-unit>
                <trans-unit id="my_collection.text.label" resname="my_collection.text.label">
                    <source>text</source>
                </trans-unit>
                <trans-unit id="my_collection.my_collection.label" resname="my_collection.my_collection.label">
                    <source>my_collection</source>
                </trans-unit>
                <trans-unit id="my_collection.my_collection.text.label" resname="my_collection.my_collection.text.label">
                    <source>text</source>
                </trans-unit>
                <trans-unit id="external_table.label" resname="external_table.label">
                    <source>external_table</source>
                </trans-unit>
                <trans-unit id="external_table_2.label" resname="external_table_2.label">
                    <source>external_table_2</source>
                </trans-unit>
                <trans-unit id="related_content.label" resname="related_content.label">
                    <source>related_content</source>
                </trans-unit>
                <trans-unit id="my-custom-key" resname="my-custom-key">
                    <source>My translation</source>
                </trans-unit>
            </body>
        </file>
    </xliff>
