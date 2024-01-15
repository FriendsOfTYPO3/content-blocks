.. include:: /Includes.rst.txt
.. _field_type_flexform_section:

Section
=======

:yaml:`type: Section`

Sections are like Collections for FlexForm. The difference is, that you can't
reference foreign types. Instead you define anonymous structures, which are only
available for this specific FlexForm field.

A Section requires you to define at least one :yaml:`Container`, which holds
the available fields. You can have multiple Containers with different fields. If
you define more than one Container, the editing interface will display multiple
buttons to choose from.

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

.. confval:: container

   :Required: true
   :Type: array

   Define one or more Containers with fields.

   identifier
      A unique identifier

   label
      Define a label. If not defined, :yaml:`identifier` is used as fallback.

   fields
      Define available fields. These field types are prohibited: :yaml:`FlexForm`, :yaml:`File`, :yaml:`Collection`.

Example:

.. code-block:: yaml

  - identifier: pi_flexform
    useExistingField: true
    fields:
      - identifier: section1
        type: Section
        label: Section 1
        container:
          - identifier: container1
            label: Container 1
            fields:
              - identifier: container_field
                type: Text
                label: Container field
          - identifier: container2
            label: Container 2
            fields:
              - identifier: container_field2
                type: Textarea
                label: Container field 2

Labels
------

XLF translation keys for Sections and Containers have the following convention:

.. code-block:: xml

    <body>
        <trans-unit id="FIELD_IDENTIFIER.sections.SECTION_IDENTIFIER.title">
            <source>Label for Section</source>
        </trans-unit>
        <trans-unit id="FIELD_IDENTIFIER.sections.SECTION_IDENTIFIER.container.CONTAINER_IDENTIFIER.title">
            <source>Label for Container</source>
        </trans-unit>
        <trans-unit id="FIELD_IDENTIFIER.sections.SECTION_IDENTIFIER.container.CONTAINER_IDENTIFIER.FIELD_IDENTIFIER.label">
            <source>Label for field in Container</source>
        </trans-unit>
    </body>
