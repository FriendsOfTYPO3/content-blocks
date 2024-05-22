.. include:: /Includes.rst.txt

.. _known-problems:

==============
Known Problems
==============

.. _row-size-too-large:

On save error: Row size too large (MariaDB)
===========================================

Explanation
-----------

There is a limit on how much can fit into a single InnoDB database row. Read `here <https://mariadb.com/kb/en/innodb-row-formats-overview/#maximum-row-size>`__ for more technical insight.
As Content Blocks uses the table :sql:`tt_content`, it must be ensured, that the table does not grow indefinitely.

Solutions
---------

First, check if you are using the `DYNAMIC row format <https://mariadb.com/kb/en/troubleshooting-row-size-too-large-errors-with-innodb/#converting-the-table-to-the-dynamic-row-format>`__.
If not, alter your tables to use this format, in order to store more data on overflow pages.

.. code-block:: sql

   ALTER TABLE tt_content ROW_FORMAT=DYNAMIC;

Else, here are some tips to save table row size:

* Reuse existing TYPO3 core and Content Blocks fields as much as possible.
* Try to minimize the usage of new :ref:`Text <field_type_text>`, :ref:`Link <field_type_link>`, :ref:`Email <field_type_email>`, :ref:`Radio <field_type_radio>`, :ref:`Color <field_type_color>` and :ref:`Select <field_type_select>` fields. They all use `varchar(255)`, :ref:`Link <field_type_link>` fields even use `varchar(1024)`.
* You can change :sql:`varchar` fields to :sql:`text`, as suggested `here <https://mariadb.com/kb/en/troubleshooting-row-size-too-large-errors-with-innodb/#converting-some-columns-to-blob-or-text>`__.
* If applicable, use :ref:`Collections <field_type_collection>`, as they create a new table.
* Otherwise consider creating an own extension with custom tables if your Content Blocks are getting too complex.

Read `this mariadb troubleshooting guide <https://mariadb.com/kb/en/troubleshooting-row-size-too-large-errors-with-innodb/>`__ for in depth explanation and more tips.

Field labels displayed as identifier
====================================

In some areas in the TYPO3 backend fields created by Content Blocks are
displayed with their identifier instead of the localized label. E.g. in the
backend user permissions view or the list view / export view. The reason is that
you can't define fields centrally in Content Blocks. Multiple Content Blocks can
reuse the same field and define alternative labels. If you really need proper
labels in these areas, we recommend to use TCA overrides in order to define a
default label.

.. code-block:: php

    $GLOBALS['TCA']['tt_content']['columns']['my_prefix_my_identifier']['label'] = 'LLL:EXT:my_extension/path/to/locallang.xlf';
