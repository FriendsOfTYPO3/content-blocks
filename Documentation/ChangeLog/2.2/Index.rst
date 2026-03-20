.. include:: /Includes.rst.txt
.. _changelog-2.2:

===
2.2
===

Content Blocks version 2.2 adds compatibility for TYPO3 v14.2.

..  contents::

Feature
=======

Allowed Record Types for Page Types
-----------------------------------

A new option :yaml:`allowedRecordTypes` has been added for Page Types, which
allows you to define a set of Record Types (tables), that should be allowed
on this specific Page Type.

Example: Extending the default allowed values with custom ones.

.. code-block:: yaml

   allowedRecordTypes:
     - pages
     - sys_category
     - sys_file_reference
     - my_custom_table

Example: Allow all records with an asterisk:

.. code-block:: yaml

   allowedRecordTypes:
     - *

Read more :ref:`here <confval-page-type-allowedRecordTypes>`.

Type specific label fields
--------------------------

The new `Core feature #108581 - Record type specific label configuration <https://docs.typo3.org/permalink/changelog:feature-108581-1735479000>`__ is implemented in Content Blocks.
This new feature allows to define alernative label fields on a per type basis.
For example, if you define `labelField` or `fallbackLabelFields` in your
Content Element, this will now override the default `header` label field. You
can now also define different label fields for multi-type Records. Previously,
the first one found was used for all types.

Example: Content Element overrides global default label field `header`.

.. code-block:: yaml

   labelField: custom_field

Example: Multi Type Record with different label fields.

.. code-block:: yaml

   typeField: type
   typeName: type1
   labelField: field_1

.. code-block:: yaml

   typeField: type
   typeName: type2
   labelField: field_1
   fallbackLabelFields:
     - field_2
