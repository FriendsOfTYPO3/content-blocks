.. include:: /Includes.rst.txt
.. _command_lint:

============
Lint command
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
