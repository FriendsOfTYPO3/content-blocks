.. include:: /Includes.rst.txt
.. _field_type_basic:

=====
Basic
=====

The :yaml:`Basic` type can be used to include a pre-defined set of fields. Read
the main article about Basics :ref:`here <basics>`.

Example:

.. code-block:: yaml
   :caption: EXT:your_extension/ContentBlocks/ContentElements/basics/EditorInterface.yaml

    name: example/basics
    basics:
        - TYPO3/Appearance
    fields:
        - identifier: header
          useExistingField: true
        - identifier: TYPO3/Links
          type: Basic
