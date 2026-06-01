.. include:: /Includes.rst.txt
.. _field_type_number:

======
Number
======

The :yaml:`Number` only allows integers or decimals as input values.

Settings
========

..  confval-menu::
    :name: confval-number-options
    :display: table
    :type:
    :default:
    :required:

.. confval:: format
   :name: number-format
   :required: false
   :type: string
   :default: 'integer'

   Possible values: `integer` (default) or `decimal`.

.. confval:: default
   :name: number-default
   :required: false
   :type: integer
   :default: "0"

   Default value set if a new record is created.

.. confval:: range
   :name: number-range
   :required: false
   :type: array

   An array which defines an integer range within the value must be.

   lower (integer)
      Defines the lower integer value.

   upper (integer)
      Defines the upper integer value.

   Example:

   .. code-block:: yaml

      range:
        lower: 10
        upper: 999

.. confval:: required
   :name: number-required
   :required: false
   :type: boolean
   :default: false

   If set, the field becomes mandatory.

.. confval:: slider
   :name: number-slider
   :required: false
   :type: array

   Render a value slider next to the field. Available keys:

   step (integer / float)
      Set the step size the slider will use. For floating point values this can
      itself be a floating point value. Default: 1.

   width (integer, pixels)
      Define the width of the slider. Default: 100.

   Example:

   .. code-block:: yaml

      slider:
        step: 1
        width: 100

   .. tip::

      It is advised to also define a range property when using the slider, otherwise the slider will go from 0 to 10000.

.. confval:: autocomplete
   :name: number-autocomplete
   :required: false
   :type: boolean

   Enables or disables browser autocomplete for the field.

.. confval:: behaviour.allowLanguageSynchronization
   :name: number-behaviour.allowLanguageSynchronization
   :required: false
   :type: boolean
   :default: false

   Allows to select if localization uses custom or default language value.

.. confval:: fieldControl
   :name: number-fieldControl
   :required: false
   :type: object

   See :ref:`TCA fieldControl <t3tca:tca_property_fieldControl>`.

.. confval:: fieldInformation
   :name: number-fieldInformation
   :required: false
   :type: object

   See :ref:`TCA fieldInformation <t3tca:tca_property_fieldInformation>`.

.. confval:: fieldWizard
   :name: number-fieldWizard
   :required: false
   :type: object

   See :ref:`TCA fieldWizard <t3tca:tca_property_fieldWizard>`.

.. confval:: mode
   :name: number-mode
   :required: false
   :type: string

   When set to :yaml:`useOrOverridePlaceholder`, a checkbox appears above the
   field allowing the user to override the placeholder value.

.. confval:: nullable
   :name: number-nullable
   :required: false
   :type: boolean
   :default: false

   Allows the database field to store a :sql:`NULL` value.

.. confval:: placeholder
   :name: number-placeholder
   :required: false
   :type: string

   Placeholder text displayed inside the field when it is empty.

.. confval:: readOnly
   :name: number-readOnly
   :required: false
   :type: boolean
   :default: false

   Renders the field in a way that the user can see the value but cannot edit it.

.. confval:: size
   :name: number-size
   :required: false
   :type: integer
   :default: 30

   Abstract width of the input field. Minimum :yaml:`10`, maximum :yaml:`50`.

.. confval:: valuePicker
   :name: number-valuePicker
   :required: false
   :type: object

   Renders a select box next to the field from which predefined values can be
   inserted. Requires an :yaml:`items` array of objects with :yaml:`label` and
   :yaml:`value` keys.

   Example:

   .. code-block:: yaml

      valuePicker:
        items:
          - label: '100'
            value: 100
          - label: '250'
            value: 250
          - label: '500'
            value: 500

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/number
    fields:
      - identifier: number
        type: Number

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/number
    fields:
      - identifier: number
        type: Number
        format: integer
        default: 10
        size: 20
        range:
          lower: 10
          upper: 999
        slider:
          step: 1
          width: 100
        valuePicker:
          items:
            - label: '100'
              value: 100
            - label: '250'
              value: 250
            - label: '500'
              value: 500
