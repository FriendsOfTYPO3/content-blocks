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

Settings
========

.. confval:: fields

   :Required: true
   :Type: array

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

You can set a :yaml:`label`, :yaml:`description` and :yaml:`linkTitle` for the
sheet either directly in the YAML config or via XLF translation keys. The link
title is displayed when hovering over the tab.

XLF translation keys for Sheets have the following convention:

.. code-block:: xml

    <body>
        <trans-unit id="FIELD_IDENTIFIER.sheets.SHEET_IDENTIFIER.label">
            <source>Label for Sheet</source>
        </trans-unit>
        <trans-unit id="FIELD_IDENTIFIER.sheets.SHEET_IDENTIFIER.description">
            <source>Description for Sheet</source>
        </trans-unit>
        <trans-unit id="FIELD_IDENTIFIER.sheets.SHEET_IDENTIFIER.linkTitle">
            <source>Link title for Sheet</source>
        </trans-unit>
    </body>

Sections
========

Sections are like Collections for FlexForm. The difference is, that you can't
reference foreign types. Instead you define anonymous structures, which are only
available for this specific FlexForm field. They also require you to set the
type :yaml:`Section` and a unique :yaml:`identifier`. Inside a Section, you
define the key :yaml:`container`, which contains a list of containers. It is
required to have at least one container. A container has two required keys:
again :yaml:`identifier` and :yaml:`fields`. A type must not be set, as it is
the only allowed type inside sections. **Not** allowed types inside container
are: :yaml:`FlexForm`, :yaml:`File`, :yaml:`Collection`, :yaml:`Sheet` and
:yaml:`Section`.

Labels
------

Labels for Sections, Container and fields inside Container have the following convention:

*  `<FlexFormIdentifier>.sections.<sectionIdentifier>.label`
*  `<FlexFormIdentifier>.sections.<sectionIdentifier>.container.<containerIdentifier>.label`
*  `<FlexFormIdentifier>.sections.<sectionIdentifier>.container.<containerIdentifier><fieldIdentifier>.label`

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/flex
    fields:
      - identifier: my_flexform
        type: FlexForm
        fields:
          - identifier: header
            type: Text
          - identifier: check
            type: Checkbox

With Sheets
-----------

.. code-block:: yaml

    name: example/flex
    fields:
      - identifier: my_flexform
        type: FlexForm
        fields:
          - identifier: sheet1
            type: Sheet
            label: Sheet 1
            description: Description for Sheet 1
            linkTitle: Link title for Sheet 1
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
                default: 0
                items:
                  - label: Option 1
                    value: 0
                  - label: Option2 2
                    value: 1

With Sections and Container
---------------------------

.. code-block:: yaml

    name: cbteam/flexform
    fields:
      - identifier: pi_flexform
        useExistingField: true
        fields:
          - type: Sheet
            identifier: sheet1
            fields:
              - identifier: link1
                type: Link
              - identifier: section1
                type: Section
                container:
                  - identifier: container1
                    fields:
                      - identifier: container_field
                        type: Text
                  - identifier: container2
                    fields:
                      - identifier: container_field2
                        type: Textarea
          - type: Sheet
            identifier: sheet2
            fields:
              - identifier: header2
                type: Text
              - identifier: textarea2
                type: Textarea
              - identifier: header1
                type: Text
