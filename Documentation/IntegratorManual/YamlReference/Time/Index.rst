.. include:: /Includes.rst.txt
.. _field_type_time:

====
Time
====

Time" type generates a simple `<input>` field, which provides a date picker.

It corresponds with the TCA `type='inputDateTime'` (default) and `eval='time'`.


Properties
==========

.. rst-class:: dl-parameters

default
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   Default value set if a new record is created.

displayAge
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` false
   :sep:`|`

   If set, enables the display of the age (p.e. “2015-08-30 (-27 days)”) of
   date fields.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (string in format `H:i`)
      Defines the lower integer value. Default: 0.

   upper (string in format `H:i`)
      Defines the upper integer value. Default: 100.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '00:01'
        upper: '29:59'

required
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the field will become mandatory.

size
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` integer
   :sep:`|` :aspect:`Default:` '20'
   :sep:`|`

   Abstract value for the width of the `<input>` field.

trim
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` boolean
   :sep:`|` :aspect:`Default:` 'false'
   :sep:`|`

   If set, the PHP trim function is applied on the field's content.

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: date
        type: Date
        properties:
          default: '15:30'
          displayAge: true
          range:
            lower: '06:01'
            upper: '17:59'
          required: false
          size: 20
          trim: true
