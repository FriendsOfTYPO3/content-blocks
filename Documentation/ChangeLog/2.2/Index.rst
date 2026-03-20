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
