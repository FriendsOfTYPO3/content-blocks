.. include:: /Includes.rst.txt
.. _field_type_number:

======
Number
======

:php:`type => 'number' // TCA`

The :yaml:`Number` only allows integers or decimals as input values.

Settings
========

.. confval:: format
   :name: number-format

   :Required: false
   :Type: string
   :Default: 'integer'

   Possible values: `integer` (default) or `decimal`.

.. confval:: default
   :name: number-default

   :Required: false
   :Type: integer
   :Default: "0"

   Default value set if a new record is created.

.. confval:: range
   :name: number-range

   :Required: false
   :Type: array

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

   :Required: false
   :Type: boolean
   :Default: false

   If set, the field becomes mandatory.

.. confval:: slider
   :name: number-slider

   :Required: false
   :Type: array

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

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-number>`.

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
            - [ '100', 100 ]
            - [ '250', 250 ]
            - [ '500', 500 ]
