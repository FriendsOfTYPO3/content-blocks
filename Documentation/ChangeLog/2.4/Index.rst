.. include:: /Includes.rst.txt
.. _changelog-2.4:

===
2.4
===

Content Blocks version 2.4 adds a collection of small new features.

..  contents::

Feature
=======

Identifier `alias` for fields
-----------------------------

You can now define an :yaml:`alias` for fields, which will then be used instead
of the :yaml:`identifier` in your Fluid templates. This has two main advantages:

1. You are not forced to use snake_case in Fluid, just because it is better
   suited for database column names.
2. You can use semantic names when re-using shared, generic fields in the
   context of your Content Block.

.. code-block:: yaml

    name: example/cta
    fields:
      - identifier: header
        alias: title

Field Type SelectText
---------------------

A new field type :ref:`SelectText <field_type_select-text>` is added. This
new type allows to have a select field with exclusively text values. The
database column will also have type :sql:`varchar(255)`, instead of
:sql:`longtext`.

..  code-block:: yaml

    name: example/select-text
    fields:
      - identifier: select_text
        type: SelectText
        items:
          - label: 'The first'
            value: 'first'
          - label: 'The second'
            value: 'second'

New option `hideInUid` for Record Types
---------------------------------------

It is now possible to explicitly hide Record Types in the record overview
by defining :yaml:`hideInUid: true`. This is already done automatically when
the Record Type is used as a child item in Collections.

New automatic language keys
---------------------------

New automatic language keys are added, which can now be used in the labels.xlf
file:

* :yaml:`placeholder` (for types with input field: Text, Textarea, Email, ...)
* :yaml:`labelChecked` (for :ref:`Checkbox <confval-checkbox-items>` with :yaml:`renderType: checkboxLabeledToggle`)
* :yaml:`labelUnchecked` (for :ref:`Checkbox <confval-checkbox-items>` with :yaml:`renderType: checkboxLabeledToggle`)

See :ref:`here <api_automatic_language_keys>` for more information.
