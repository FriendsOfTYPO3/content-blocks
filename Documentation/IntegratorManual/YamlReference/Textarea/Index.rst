.. include:: /Includes.rst.txt
.. _field_type_textarea:

========
Textarea
========

The `Textarea` type is for multi line text input. A Rich Text Editor can be
enabled.

It corresponds with the TCA :php:`type => 'text'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

enableRichtext
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set to true, the system renders a Rich Text Editor if that is enabled for
   the editor (default: yes), and if a suitable editor extension is loaded
   (default: rte_ckeditor).

   If either of these requirements is not met, the system falls back to
   a `<textarea>` field.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

placeholder
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Placeholder text for the field.

richtextConfiguration
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   The value is a key in :php:`$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']`
   array and specifies the YAML configuration source field used for that RTE
   field. It does not make sense without having property :yaml:`enableRichtext`
   set to true.

   Extension `rte_ckeditor` registers three presets: `default`, `minimal` and
   `full` and points to YAML files with configuration details.

   Integrators may override for instance the `default` key to point to an own
   YAML file which will affect all core backend RTE instances to use that
   configuration.

   If this property is not specified for an RTE field, the system will fall back
   to the `default` configuration.

rows
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '5'
   :sep:`|`

   Abstract value for the height of the `<textarea>` field. Max value is 20.

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

Examples
========

Minimal
-------

.. code-block:: yaml

    group: common
    fields:
      - identifier: textarea
        type: Textarea

Advanced / use case
-------------------

.. code-block:: yaml

    group: common
    fields:
      - identifier: textarea
        type: Textarea
        properties:
          enableRichtext: true
          richtextConfiguration: full
          default: 'Default value'
          placeholder: 'Placeholder text'
          rows: 15
