.. include:: /Includes.rst.txt
.. _field_type_flexform_sheet:

Sheet
=====

:yaml:`type: Sheet`

Sheets are used to further group FlexForm fields into separate tabs. Note that
you need at least 2 Sheets for a tab navigation to appear in the backend. This
is purely cosmetical and, like Palettes and Tabs, has no effect on frontend
rendering.

..  warning::
    Due to the fact that FlexForm is stored as XML in the database, changing the
    Sheet identifiers (or moving fields into other Sheets) retrospectively is
    destructive. You will lose your data.

Settings
--------

.. confval:: identifier

   :Required: true
   :Type: string

   A unique identifier

.. confval:: label

   :Required: false
   :Type: string

   Define a label. If not defined, :yaml:`identifier` is used as fallback.

.. confval:: description

   :Required: false
   :Type: string

   Define a description.

.. confval:: linkTitle

   :Required: false
   :Type: string

   The link title is displayed when hovering over the tab.

Example:

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
          - identifier: sheet2
            type: Sheet
            label: Sheet 2
            fields:
              - identifier: link
                type: Link

Labels
------

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
