.. include:: /Includes.rst.txt
.. _command_generate_frontend_template:

==================================
Generate Frontend Template Command
==================================

The command :bash:`content-blocks:generate:frontend-template` generates a
frontend Fluid template for the given Content Element Content Block and writes
it to the :file:`templates/frontend.fluid.html` file inside the Content Block
directory. The file will not be overwritten unless :bash:`--force` is provided.

Arguments
=========

.. confval:: content-block
   :name: generate-frontend-template-content-block

   :Required: true
   :Type: string

   The Content Block to generate the frontend template for (e.g. `vendor/name`).

Options
=======

.. confval:: force
   :name: generate-frontend-template-force

   :Shortcut: f
   :Type: bool

   Override an existing frontend template file.

Generate a frontend template for Content Block `example/my-block`:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:generate:frontend example/my-block

Override an existing frontend template:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:generate:frontend example/my-block --force
