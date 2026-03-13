.. include:: /Includes.rst.txt
.. _changelog-1.4:

===
1.4
===

Content Blocks version 1.4 introduces a complete JSON Schema for all Content
Block types.

This feature was voted for as part of the
`Community Budget Ideas 2026 Round One <https://talk.typo3.org/t/content-blocks-json-schema-yaml-linter-nikita-hovratov/6593>`__

..  contents::

Feature
=======

JSON Schema
-----------

Since Content Blocks is based on a YAML definition, it is possible to validate
it against a `JSON Schema <https://json-schema.org/>`__. The schema is
shipped directly inside the Content Blocks extension and updated in each new
releases accordingly. It is located in the root folder "JsonSchema":

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

Learn how to use it in your IDE :ref:`here <json-schema-ide>`.

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
