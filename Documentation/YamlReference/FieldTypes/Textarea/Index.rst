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

   Placeholder text for the field. Can also be used as automatic language key
   in labels.xlf. See :ref:`here <api_automatic_language_keys>` for more
   information.

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

.. confval:: behaviour.allowLanguageSynchronization
   :name: textarea-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: cols
   :name: textarea-cols
   :required: false
   :type: integer
   :default: 30

   Abstract width of the textarea field. Minimum :yaml:`10`, maximum :yaml:`50`.

.. confval:: enableTabulator
   :name: textarea-enableTabulator
   :required: false
   :type: boolean
   :default: false

   Allows the use of tab characters inside the textarea.

.. confval:: eval
   :name: textarea-eval
   :required: false
   :type: string

   Configuration of field evaluation. For example :yaml:`trim` to strip
   whitespace from the value before saving.

.. confval:: fieldControl
   :name: textarea-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: textarea-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: textarea-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: fixedFont
   :name: textarea-fixedFont
   :required: false
   :type: boolean

   Renders the textarea with a fixed-width (monospace) font.

.. confval:: is_in
   :name: textarea-is_in
   :required: false
   :type: string

   Evaluates whether the entered text contains only characters from this
   string.

.. confval:: max
   :name: textarea-max
   :required: false
   :type: integer

   Maximum number of characters allowed.

.. confval:: min
   :name: textarea-min
   :required: false
   :type: integer

   Minimum number of characters required. Empty values are still allowed;
   combine with :yaml:`required` to enforce a non-empty value.

.. confval:: nullable
   :name: textarea-nullable
   :required: false
   :type: boolean
   :default: false

   Allows the database field to store a :sql:`NULL` value.

.. confval:: readOnly
   :name: textarea-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: renderType
   :name: textarea-renderType
   :required: false
   :type: string

   Selects an alternative rendering for the textarea. Possible values:

   *  :yaml:`codeEditor`
   *  :yaml:`textTable`
   *  :yaml:`belayoutwizard`

.. confval:: valuePicker
   :name: textarea-valuePicker
   :required: false
   :type: object

   Renders a select box next to the field from which predefined values can be
   inserted. Requires an :yaml:`items` array of objects with :yaml:`label` and
   :yaml:`value` keys.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          - label: 'Job offer general'
            value: 'Want to join our team? Take the initiative!'
          - label: 'Job offer specific'
            value: 'We are looking for ...'

.. confval:: wrap
   :name: textarea-wrap
   :required: false
   :type: string
   :default: virtual

   Wrapping mode of the textarea. Possible values:

   *  :yaml:`virtual`
   *  :yaml:`off`

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
