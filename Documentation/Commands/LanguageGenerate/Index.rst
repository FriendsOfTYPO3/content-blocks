.. include:: /Includes.rst.txt
.. _command_language_generate:

=========================
Language Generate command
=========================

The command :bash:`content-blocks:language:generate` updates the labels.xlf
content for the specified Content Block. Already set labels in both the
labels.xlf file and in the config.yaml are considered. The labels.xlf
file has precedence over inline labels in the YAML definition. Optional keys
like descriptions or labels for existing fields will only be be generated if
they have been set manually. Custom translations, which don't belong to the
automatic language keys, will be kept and appended to the end.

Arguments
=========

.. confval:: content-block
   :name: language-generate-content-block

   :Required: true (false if :bash:`--extension` provided)
   :Type: string

   The Content Block to generate the xlf for.

Options
=======

.. confval:: print
   :name: language-generate-print

   :Shortcut: p
   :Type: bool

   Print labels.xlf to terminal instead of writing to file system.

.. confval:: extension
   :name: language-generate-extension

   :Shortcut: e
   :Type: string

   Write labels.xlf to all Content Blocks within the given extension.

Write up-to-date labels.xlf file for Content Block example/example.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example

Update all labels.xlf files within the extension "site_package".

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example --extension="site_package"

Print up-to-date labels.xlf content for Content Block example/example.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:language:generate example/example --print

Example output:

.. code-block:: xml

    <?xml version="1.0"?>
    <xliff version="1.2" xmlns="urn:oasis:names:tc:xliff:document:1.2">
        <file datatype="plaintext" original="labels.xlf" source-language="en" date="2023-12-03T08:37:53+00:00" product-name="demo/demo">
            <header/>
            <body>
                <trans-unit id="title">
                    <source>My demo title</source>
                </trans-unit>
                <trans-unit id="description">
                    <source>This is just a demo/demo</source>
                </trans-unit>
                <trans-unit id="header.label">
                    <source>Existing field override</source>
                </trans-unit>
                <trans-unit id="slug.label">
                    <source>My Slug</source>
                </trans-unit>
                <trans-unit id="slug.description">
                    <source>My Slug Description</source>
                </trans-unit>
                <trans-unit id="my_collection.label">
                    <source>my_collection</source>
                </trans-unit>
                <trans-unit id="my_collection.text.label">
                    <source>text</source>
                </trans-unit>
                <trans-unit id="my_collection.my_collection.label">
                    <source>my_collection</source>
                </trans-unit>
                <trans-unit id="my_collection.my_collection.text.label">
                    <source>text</source>
                </trans-unit>
                <trans-unit id="external_table.label">
                    <source>external_table</source>
                </trans-unit>
                <trans-unit id="external_table_2.label">
                    <source>external_table_2</source>
                </trans-unit>
                <trans-unit id="related_content.label">
                    <source>related_content</source>
                </trans-unit>
                <trans-unit id="my-custom-key">
                    <source>My translation</source>
                </trans-unit>
            </body>
        </file>
    </xliff>
