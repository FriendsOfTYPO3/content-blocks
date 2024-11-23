.. include:: /Includes.rst.txt
.. _field_type_basic:

=====
Basic
=====

The :yaml:`Basic` type can be used to include a pre-defined set of fields. Most
commonly used to include shared :ref:`Palettes <field_type_palette>`.

Can also be used as a root option :ref:`basics <confval-root-basics>`.

Read the main article about :ref:`Basics <basics>`.

Example:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/basics/config.yaml

    name: example/basics
    fields:
        - identifier: TYPO3/Header
          type: Basic
