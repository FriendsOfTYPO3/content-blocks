.. include:: /Includes.rst.txt
.. _cb_definition_editor_interface:

====================
EditorInterface.yaml
====================

The heart of a Content Block is the **EditorInterface.yaml** file. This YAML
file defines both the available fields and the structure:

.. code-block:: yaml
   :caption: EXT:some_extension/ContentBlocks/ContentElements/content-block-name

    name: vendor/content-block-name
    fields:
      - identifier: header
        useExistingField: true
      - identifier: my_text_field
        type: Text
        max: 10

First of all, a :yaml:`name` has to be defined. It must be unique inside your
installation. It consists, similarly to composer package names, of a vendor and
a package part separated by a slash. It is used to prefix new field names, new
tables and record type identifiers.

Inside :yaml:`fields` you define the structure and configuration of the
necessary fields. The :yaml:`identifier` has to be unique per Content Block.

It is possible to reuse existing fields with the flag :yaml:`useExistingField`.
This allows e.g. to use the same field :sql:`header` or :sql:`bodytext` across
multiple Content Blocks with different configuration. Be aware that system
fields shouldn't be reused. A list of sane reusable fields can be referenced in
the documentation. Furthermore, own custom fields can be reused as well.

*  Refer to the :ref:`YAML reference <yaml_reference>` for a complete overview.
*  Learn more about :ref:`reusing fields <cb_reuse_existing_fields>`
*  Learn how to :ref:`extend TCA <cb_extendTca>` of Content Blocks (for advanced users).
*  For more information about the YAML syntax refer to `YAML RFC <https://github.com/yaml/summit.yaml.io/wiki/YAML-RFC-Index>`__
