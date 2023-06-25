.. include:: /Includes.rst.txt
.. _field_type_number:

======
Number
======

The `Number` only allows integers or decimals as input values.

It corresponds with the TCA :php:`type => 'number'`.

SQL overrides via `alternativeSql` allowed: yes.

Properties
==========

.. rst-class:: dl-parameters

format
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` 'int'
   :sep:`|`

   Possible values: `integer` (default) or `decimal`.

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` 0
   :sep:`|`

   Default value set if a new record is created.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (integer)
      Defines the lower integer value. Default: 0.

   upper (integer)
      Defines the upper integer value. Default: none.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: 10
        upper: 999

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

slider
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Render a value slider next to the field. Available keys:

   step (integer / float)
      Set the step size the slider will use. For floating point values this can
      itself be a floating point value. Default: 1.

   width (integer, pixels)
      Define the width of the slider. Default: 100.

   Example:

   .. code-block:: yaml

      range:
        step: 1
        width: 100

For more advanced configuration refer to the :ref:`TCA documentation <t3tca:columns-number>`.

Examples
========

Minimal
-------

.. code-block:: yaml

    name: example/number
    group: common
    fields:
      - identifier: number
        type: Number

Advanced / use case
-------------------

.. code-block:: yaml

    name: example/number
    group: common
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
        range:
          step: 1
          width: 100
        valuePicker:
        items:
          [
            [ '100', 100 ],
            [ '250', 250 ],
            [ '500', 500 ],
          ]
