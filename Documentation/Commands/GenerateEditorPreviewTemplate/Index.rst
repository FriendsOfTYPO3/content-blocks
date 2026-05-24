.. include:: /Includes.rst.txt
.. _command_generate_backend_preview_template:

=========================================
Generate Backend Preview Template Command
=========================================

The command :bash:`content-blocks:generate:editor-preview` generates a
backend backend preview Fluid template for the given Content Block and writes it
to the :file:`templates/backend-preview.fluid.html` file inside the Content
Block directory. The file will not be overwritten unless :bash:`--force` is
provided.

Arguments
=========

.. confval:: content-block
   :name: generate-backend-preview-content-block

   :Required: true
   :Type: string

   The Content Block to generate the backend preview template for (e.g. `vendor/name`).

Options
=======

.. confval:: force
   :name: generate-backend-preview-force

   :Shortcut: f
   :Type: bool

   Override a existing backend preview template file.

Generate a backend preview template for Content Block `example/my-block`:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:generate:backend-preview example/my-block

Override a existing backend preview template:

.. code-block:: bash

   vendor/bin/typo3 content-blocks:generate:backend-preview example/my-block --force
