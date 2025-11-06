.. include:: /Includes.rst.txt
.. _field_type_textarea:

========
Textarea
========

The :yaml:`Textarea` type is for multi line text input. A Rich Text Editor can
be enabled.

Settings
========

..  confval-menu::
    :name: confval-textarea-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: default
   :name: textarea-default
   :required: false
   :type: string

   Default value set if a new record is created.

.. confval:: placeholder
   :name: textarea-placeholder
   :required: false
   :type: string

   Placeholder text for the field.

.. confval:: rows
   :name: textarea-rows
   :required: false
   :type: integer
   :default: 5

   Abstract value for the height of the `<textarea>` field. Max value is 20.

.. confval:: required
   :name: textarea-required
   :required: false
   :type: boolean
   :default: false

   If set, the field will become mandatory.

.. confval:: searchable
   :name: textarea-searchable
   :required: false
   :type: boolean
   :default: true

   If set to false, the field will not be considered in backend search.

.. confval:: enableRichtext
   :name: textarea-enableRichtext
   :required: false
   :type: boolean
   :default: false

   If set to true, the system renders a Rich Text Editor if that is enabled for
   the editor (default: yes), and if a suitable editor extension is loaded
   (default: rte_ckeditor).

   If either of these requirements is not met, the system falls back to
   a `<textarea>` field.

.. confval:: richtextConfiguration
   :name: textarea-richtextConfiguration
   :required: false
   :type: string

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
