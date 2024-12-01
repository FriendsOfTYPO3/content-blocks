.. include:: /Includes.rst.txt
.. _cb_extendTca:

==========
Extend TCA
==========

Content Blocks generates a lot of boilerplate :ref:`TCA <t3tca:start>`
(Table Configuration Array) for you. Usually you don't need to write own TCA,
but in some cases, where you want to override the TCA from Content Blocks, you
can do it with own TCA overrides.

How to override Content Blocks TCA
==================================

Finding the correct identifier
------------------------------

In order to find out the internal identifier, which is used in TCA you need to
know how :ref:`prefixing <api_prefixing>` works in Content Blocks. You can also
simply inspect the TCA in the Configuration module of the `typo3/cms-lowlevel`
extension.

Fields in tt_content
--------------------

It works exactly like overriding core TCA.

Example:

.. code-block:: php
   :caption: EXT:sitepackage/Configuration/TCA/Overrides/tt_content.php

   $GLOBALS['TCA']['tt_content']['columns']['myvendor_mycontentblock_header']['config']['some_option'] = 'some_value';
   $GLOBALS['TCA']['tt_content']['types']['myvendor_mycontentblock']['some_option'] = 'some_value';

Fields in custom tables / record types
--------------------------------------

As soon as you create a :ref:`Collection <field_type_collection>` field,
Content Blocks creates a new custom table. Therefore you need to change the key
to the table's name. Extend the TCA in `Configuration/TCA/Overrides/myvendor_mycontentblock_mycollection.php`.
For record types you already defined a :yaml:`tableName`, so use this as the key.

Example:

.. code-block:: php
   :caption: EXT:sitepackage/Configuration/TCA/Overrides/myvendor_mycontentblock_mycollection.php

   $GLOBALS['TCA']['myvendor_mycontentblock_mycollection']['columns']['your_field']['config']['some_option'] = 'some_value';

.. code-block:: php
   :caption: EXT:sitepackage/Configuration/TCA/Overrides/my_record_type_table.php

   $GLOBALS['TCA']['my_record_type_table']['columns']['your_field']['config']['some_option'] = 'some_value';
