.. include:: /Includes.rst.txt
.. _field_type_textarea:

========
Textarea
========

:php:`type => 'text' // TCA`

The :yaml:`Textarea` type is for multi line text input. A Rich Text Editor can
be enabled.

Settings
========

.. confval:: default

   :Required: false
   :Type: string

   Default value set if a new record is created.

.. confval:: placeholder

   :Required: false
   :Type: string

   Placeholder text for the field.

.. confval:: rows

   :Required: false
   :Type: integer
   :Default: 5

   Abstract value for the height of the `<textarea>` field. Max value is 20.

.. confval:: required

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field will become mandatory.

.. confval:: enableRichtext

   :Required: false
   :Type: boolean
   :Default: false

   If set to true, the system renders a Rich Text Editor if that is enabled for
   the editor (default: yes), and if a suitable editor extension is loaded
   (default: rte_ckeditor).

   If either of these requirements is not met, the system falls back to
   a `<textarea>` field.

.. confval:: richtextConfiguration

   :Required: false
   :Type: string

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

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/textarea
    fields:
      - identifier: textarea
        type: Textarea

Richtext field
--------------

.. code-block:: yaml

    name: example/richtext
    fields:
      - identifier: textarea
        type: Textarea
        enableRichtext: true
        richtextConfiguration: full

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/textarea
    fields:
      - identifier: textarea
        type: Textarea
        default: "Default value"
        placeholder: "Placeholder text"
        required: true
        rows: 15
