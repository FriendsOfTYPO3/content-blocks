.. include:: /Includes.rst.txt
.. _json-schema:

===========
Json Schema
===========

.. versionadded:: 2.1

Since Content Blocks is based on a YAML definition, it is possible to validate
it against a `JSON Schema <https://json-schema.org/>`__. The schema is
shipped directly inside the Content Blocks extension and updated in each new
releases accordingly. It is located in the root folder "JsonSchema":

.. note::

    This feature was voted for as part of the `Community Budget Ideas 2026 Round One <https://talk.typo3.org/t/content-blocks-json-schema-yaml-linter-nikita-hovratov/6593>`__

..  card::
    :class: mb-4

    ..  directory-tree::
        :level: 3

            *   :path:`content-blocks`

                *   :path:`JsonSchema`

                    *   :file:`basic.schema.json`

                    *   :file:`content-element.schema.json`

                    *   :file:`file-type.schema.json`

                    *   :file:`page-type.schema.json`

                    *   :file:`record-type.schema.json`

As you can see, each content type brings its own schema. Even :ref:`Basics <basics>`
have their own schema, as they are separate YAML files.

Usage
=====

Lint Command
------------

The most simple usage is the integrated :ref:`lint command <command_lint>`:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:lint

Running this command will reveal configuration errors of all loaded Content
Blocks. Use this in your CI pipeline to ensure you don't introduce incorrect
configuration.

.. note::

   This command only works in composer-mode.

IDE Integration
---------------

When writing Content Blocks configuration, an IDE integration of the JSON Schema
will help you with auto-completion and realtime linting.

PhpStorm
++++++++

Open PhpStorm and switch to File > Preferences > Languages & Frameworks > Schemas and DTDs > JSON Schema Mappings.
Then, select the local json file for "Schema file or URL" in your vendor/friendsoftypo3/content-blocks or typo3conf/ext/content_blocks folder.
Lastly, create a new "File path Pattern". For example: `**/ContentBlocks/ContentElements/*/config.yaml`.
Do this for each type you want validation for. Unfortunately, you have to repeat
these steps for each new project.

Overview:

+------------------+----------------------------------------+------------------------------------------------+
| Content Type     | Schema file                            | File path Pattern                              |
+==================+========================================+================================================+
| Content Elements | JsonSchema/content-element.schema.json | **/ContentBlocks/ContentElements/*/config.yaml |
+------------------+----------------------------------------+------------------------------------------------+
| Page Types       | JsonSchema/page-type.schema.json       | **/ContentBlocks/PageTypes/*/config.yaml       |
+------------------+----------------------------------------+------------------------------------------------+
| Record Types     | JsonSchema/record-type.schema.json     | **/ContentBlocks/RecordTypes/*/config.yaml     |
+------------------+----------------------------------------+------------------------------------------------+
| File Types       | JsonSchema/file-type.schema.json       | **/ContentBlocks/FileTypes/*/config.yaml       |
+------------------+----------------------------------------+------------------------------------------------+
| Basics           | JsonSchema/basic.schema.json           | **/ContentBlocks/Basics/*.yaml                 |
+------------------+----------------------------------------+------------------------------------------------+

Visual Studio Code
++++++++++++++++++

1. Install the plugin "redhat.vscode-yaml"
2. Open the settings and search for "yaml schemas" and open the settings.json.
3. Add the following config (adjust the local path accordingly)

.. code-block:: json

    {
        "yaml.schemas": {
            "vendor/friendsoftypo3/content-blocks/JsonSchema/content-element.schema.json" : ["**/ContentBlocks/ContentElements/*/config.yaml"],
            "vendor/friendsoftypo3/content-blocks/JsonSchema/page-type.schema.json" : ["**/ContentBlocks/PageTypes/*/config.yaml"],
            "vendor/friendsoftypo3/content-blocks/JsonSchema/record-type.schema.json" : ["**/ContentBlocks/RecordTypes/*/config.yaml"],
            "vendor/friendsoftypo3/content-blocks/JsonSchema/file-type.schema.json" : ["**/ContentBlocks/FileTypes/*/config.yaml"],
            "vendor/friendsoftypo3/content-blocks/JsonSchema/basic.schema.json" : ["**/ContentBlocks/Basics/*.yaml"]
        }
    }
