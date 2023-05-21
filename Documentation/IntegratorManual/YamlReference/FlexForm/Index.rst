.. include:: /Includes.rst.txt
.. _field_type_flexform:

========
FlexForm
========

The `FlexForm` field allows to group multiple fields into one database column.
It is mostly used to store configuration options, rather than actual content.
By using FlexForm it is possible to conserve the database, but it also has its
limitations. For example nesting of Collections is disallowed.

It corresponds with the TCA :php:`type => 'flex'`.

SQL overrides via `alternativeSql` allowed: no.

Top level settings
==================

.. rst-class:: dl-parameters

fields
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` array
   :sep:`|`

   Similar to `Collections` you define the fields to be used inside the FlexForm
   definition.

Sheets
======

Sheets are used to further group FlexForm fields into separate tabs. This is
done by defining the type :yaml:`Sheet`, which itself can hold further
:yaml:`fields`. It is mandatory to define an :yaml:`identifier` for the Sheet.
See the advanced example on how to use it. Note that you need at
least 2 Sheets for a tab navigation to appear in the backend. This is purely
cosmetical and, like Palettes and Tabs, has no effect on Frontend rendering.

..  warning::
    Due to the fact that FlexForm is stored as XML in the database, changing the
    Sheet identifiers (or moving fields into other Sheets) retrospectively is
    destructive. You will lose your data.

Labels
------

Labels for Sheets have the following convention:

*  <FlexFormIdentifier>.sheets.<SheetIdentifier>.<label>
*  <FlexFormIdentifier>.sheets.<SheetIdentifier>.<description>
*  <FlexFormIdentifier>.sheets.<SheetIdentifier>.<linkTitle> (link title of the tabs)

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/flex
    group: common
    fields:
      - identifier: my_flexform
        type: FlexForm
        fields:
          - identifier: header
            type: Text
          - identifier: check
            type: Checkbox

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/flex
    group: common
    fields:
      - identifier: my_flexform
        type: FlexForm
        fields:
          - identifier: sheet1
            type: Sheet
            fields:
              - identifier: header
                type: Text
              - identifier: check
                type: Checkbox
          - identifier: sheet2
            type: Sheet
            fields:
              - identifier: link
                type: Link
              - identifier: radio
                type: Radio
