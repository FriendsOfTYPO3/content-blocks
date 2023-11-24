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

.. note::

   First make sure, you've added Content Blocks as a dependency in your
   extension.

Finding the correct identifier
------------------------------

The identifier to use in TCA depends on whether the Content Block uses
:yaml:`prefixFields` or not. If this feature is enabled, your field identifiers
are prefixed with the vendor and content block name. Example:
:yaml:`my-vendor/my-content-block` and field identifier :yaml:`header` result
in :php:`myvendor_mycontentblock_header`. See how dashes are removed and the two
parts are glued together with an underscore. The same goes for the table name of
:yaml:`Collection` fields. `myvendor_mycontentblock` is also the resulting
:yaml:`typeName`, if not set explicitly. This can be used to override the TCA
:php:`types` array. Otherwise, the field and table identifiers defined in the
YAML config are identical to the TCA one.

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
