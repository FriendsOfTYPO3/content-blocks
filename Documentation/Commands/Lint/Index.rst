.. include:: /Includes.rst.txt
.. _command_lint:

============
Lint Command
============

The command :bash:`content-blocks:lint` finds all configuration errors inside
your Content Blocks based on the JSON Schema. This command is especially useful
in CI/CD pipelines.

.. code-block:: bash

   vendor/bin/typo3 content-blocks:lint

Example output:

.. code-block:: bash

    +-----------------------------+---------------------------------------------------+
    | Path                        | example/card-group | EXT:content_blocks_examples  |
    +-----------------------------+---------------------------------------------------+
    | /fields/3/fields/3/fields/0 | Additional object properties are not allowed: max |
    +-----------------------------+---------------------------------------------------+


     [ERROR] Found 1 errors

:ref:`Basics <basics>` and :ref:`Tabs <api_tabs>` are validated against their
own JSON Schema as well. Errors in Tab files are reported with the file name
of the Tab:

.. code-block:: bash

    +------+--------------------------------------------------------------------+
    | Path | example/card-group tabs/settings.yaml | EXT:content_blocks_examples |
    +------+--------------------------------------------------------------------+
    | /    | Additional object properties are not allowed: title                |
    +------+--------------------------------------------------------------------+
