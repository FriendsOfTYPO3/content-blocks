.. include:: /Includes.rst.txt
.. _field_type_date:

====
Date
====

The "Date" type generates a simple `<input>` field, which provides a date picker.

It corresponds with the TCA `type='inputDateTime'` (default) and `eval='date'`.


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
   :sep:`|` :aspect:`Default:` true
   :sep:`|`

   If set, enables the display of the age (p.e. “2015-08-30 (-27 days)”)
   of date fields. Invert state of TCA's disableAgeDisplay property.

range
   :sep:`|` :aspect:`Required:` false
   :sep:`|` :aspect:`Type:` array
   :sep:`|` :aspect:`Default:` ''
   :sep:`|`

   An array which defines an integer range within which the value must be. Keys:

   lower (string in format `dd-mm-yyyy`)
      Defines the lower integer value. Default: 0.

   upper (string in format `dd-mm-yyyy`)
      Defines the upper integer value. Default: 100.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '01-01-1970'
        upper: '31-12-2020'

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
          default: '2020-12-12'
          displayAge: true
          range:
            lower: '2019-12-12'
            upper: '2035-12-12'
          required: false
          size: 20
          trim: true
