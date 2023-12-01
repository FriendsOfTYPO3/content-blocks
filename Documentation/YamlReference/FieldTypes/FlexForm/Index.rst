.. include:: /Includes.rst.txt
.. _field_type_flexform:

========
FlexForm
========

:php:`type => 'flex' // TCA`

The :yaml:`FlexForm` field allows you to group multiple fields into one database
column. It is mostly used to store configuration options, rather than actual
content. By using FlexForm it is possible to conserve the database, but it also
has its limitations. For example nesting of Collections is disallowed.

.. toctree::
    :maxdepth: 1
    :titlesonly:

    Sheet/Index
    Section/Index

Settings
========

.. confval:: fields

   :Required: true
   :Type: array

   Fields to be used inside the FlexForm definition.

Sheets, Sections and Containers
===============================

FlexForm provides a way to organize your fields in **Sheets**. Sheets create a
tab navigation in the editing interface. Learn more about :ref:`Sheets <field_type_flexform_sheet>`.

An alternative way of defining repeating content are **Sections**. Sections can
have multiple **Containers**. Learn more about :ref:`Sections <field_type_flexform_section>`.

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
            label: Sheet 2
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

With Sections and Containers
----------------------------

.. code-block:: yaml

    name: cbteam/flexform
    fields:
      - identifier: pi_flexform
        useExistingField: true
        label: My FlexForm field
        fields:
          - type: Sheet
            identifier: sheet1
            label: Sheet 1
            fields:
              - identifier: link1
                type: Link
              - identifier: section1
                type: Section
                label: Section 1
                container:
                  - identifier: container1
                    label: Container 1
                    fields:
                      - identifier: container_field
                        type: Text
                  - identifier: container2
                    label: Container 2
                    fields:
                      - identifier: container_field2
                        type: Textarea
          - type: Sheet
            identifier: sheet2
            label: Sheet 2
            fields:
              - identifier: header2
                type: Text
              - identifier: textarea2
                type: Textarea
              - identifier: header1
                type: Text
