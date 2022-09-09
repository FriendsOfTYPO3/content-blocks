.. include:: /Includes.rst.txt
.. _field_type_datetime:

========
DateTime
========

The "DateTime" type generates a simple `<input>` field, which provides a date picker.

It corresponds with the TCA `type='inputDateTime'` (default) and `eval='date'`,
`eval='time'` or `eval='datetime'` depending on the sub-type.


Properties
==========

.. rst-class:: dl-parameters

subType
   :sep:`|` :aspect:`Required:` true
   :sep:`|` :aspect:`Type:` string
   :sep:`|` :aspect:`Default:` 'datetime'
   :sep:`|`

    Possible values: `date`, `time` or `datetime`

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

   lower (string in format `H:i dd-mm-yyyy`)
      Defines the lower integer value. Default: 0.

   upper (string in format `H:i dd-mm-yyyy`)
      Defines the upper integer value. Default: 100.

   It is allowed to specify only one of both of them.

   Example:

   .. code-block:: yaml

      range:
        lower: '00:01 01-01-1970'
        upper: '29:59 31-12-2020'

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

Example
=======

.. code-block:: yaml

    group: common
    fields:
      - identifier: datetime
        type: DateTime
        properties:
          default: '2020-12-12'
          displayAge: true
          range:
            lower: '2019-01-31 12:00:00'
            upper: '2040-01-31 12:00:00'
          required: false
          size: 20
